<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushNotificationService
{
    /**
     * Envía la notificación del pago y devuelve
     * información detallada de cada intento.
     */
    public function sendPaymentNotification(
        Payment $payment
    ): array {
        $subject = trim(
            (string) config(
                'services.webpush.subject'
            )
        );

        $publicKey = trim(
            (string) config(
                'services.webpush.public_key'
            )
        );

        $privateKey = trim(
            (string) config(
                'services.webpush.private_key'
            )
        );

        if (
            $subject === '' ||
            $publicKey === '' ||
            $privateKey === ''
        ) {
            throw new \RuntimeException(
                'La configuración VAPID está incompleta.'
            );
        }

        $auth = [
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        $webPush = new WebPush(
            auth: $auth,
            defaultOptions: [
                'TTL' => 300,
                'urgency' => 'high',
                'topic' => null,
                'batchSize' => 1000,
            ]
        );

        $payment->loadMissing('provider');

        $payload = json_encode(
            [
                'title' =>
                    'Nuevo ' .
                    (
                        $payment->provider?->name
                        ?? 'pago'
                    ) .
                    ': S/ ' .
                    number_format(
                        (float) $payment->amount,
                        2,
                        '.',
                        ''
                    ),

                'body' =>
                    (
                        $payment->payer_name
                        ?: 'Cliente no identificado'
                    ) .
                    ' realizó un pago.',

                'icon' =>
                    '/logo-icon-192.png',

                'badge' =>
                    '/logo-icon-192.png',

                'tag' =>
                    'miorpa-payment-' .
                    $payment->public_id,

                'url' =>
                    route(
                        'business.payments.show',
                        $payment
                    ),

                'payment_id' =>
                    $payment->public_id,

                'payment' => [
                    'public_id' =>
                        $payment->public_id,

                    'provider' =>
                        $payment->provider?->name
                        ?? 'Pago',

                    'amount' =>
                        number_format(
                            (float) $payment->amount,
                            2,
                            '.',
                            ''
                        ),

                    'payer_name' =>
                        $payment->payer_name
                        ?: 'Cliente no identificado',

                    'detail_url' =>
                        route(
                            'business.payments.show',
                            $payment
                        ),
                ],
            ],
            JSON_THROW_ON_ERROR
        );

        $storedSubscriptions =
            PushSubscription::query()
                ->whereHas(
                    'user',
                    function ($query) use ($payment) {
                        $query->where(
                            'business_id',
                            $payment->business_id
                        );
                    }
                )
                ->get();

        $results = [];

        foreach (
            $storedSubscriptions
            as $storedSubscription
        ) {
            try {
                $subscription =
                    Subscription::create([
                        'endpoint' =>
                            $storedSubscription->endpoint,

                        'publicKey' =>
                            $storedSubscription->public_key,

                        'authToken' =>
                            $storedSubscription->auth_token,

                        'contentEncoding' =>
                            $storedSubscription
                                ->content_encoding
                                ?: 'aes128gcm',
                    ]);

                $webPush->queueNotification(
                    $subscription,
                    $payload,
                    [
                        'TTL' => 300,
                        'urgency' => 'high',
                    ]
                );
            } catch (Throwable $exception) {
                $results[] = [
                    'subscription_id' =>
                        $storedSubscription->id,

                    'success' => false,

                    'expired' => false,

                    'status_code' => null,

                    'reason' =>
                        $exception->getMessage(),
                ];

                Log::error(
                    'No se pudo preparar Web Push.',
                    [
                        'payment_id' =>
                            $payment->public_id,

                        'subscription_id' =>
                            $storedSubscription->id,

                        'error' =>
                            $exception->getMessage(),
                    ]
                );
            }
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = (string)
                $report
                    ->getRequest()
                    ->getUri();

            $statusCode =
                $report->getResponse()
                    ?->getStatusCode();

            $result = [
                'endpoint' =>
                    mb_substr(
                        $endpoint,
                        0,
                        100
                    ),

                'success' =>
                    $report->isSuccess(),

                'expired' =>
                    $report
                        ->isSubscriptionExpired(),

                'status_code' =>
                    $statusCode,

                'reason' =>
                    $report->getReason(),
            ];

            $results[] = $result;

            if ($report->isSuccess()) {
                Log::info(
                    'Web Push enviado correctamente.',
                    [
                        'payment_id' =>
                            $payment->public_id,

                        'status_code' =>
                            $statusCode,
                    ]
                );

                continue;
            }

            Log::error(
                'Web Push rechazado.',
                [
                    'payment_id' =>
                        $payment->public_id,

                    'status_code' =>
                        $statusCode,

                    'reason' =>
                        $report->getReason(),

                    'expired' =>
                        $report
                            ->isSubscriptionExpired(),

                    'endpoint' =>
                        $endpoint,
                ]
            );

            if (
                $report->isSubscriptionExpired()
            ) {
                PushSubscription::query()
                    ->where(
                        'endpoint',
                        $endpoint
                    )
                    ->delete();
            }
        }

        return [
            'payment_id' =>
                $payment->public_id,

            'business_id' =>
                $payment->business_id,

            'subscriptions_found' =>
                $storedSubscriptions->count(),

            'results' =>
                $results,
        ];
    }
}