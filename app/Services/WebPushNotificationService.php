<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushNotificationService
{
    public function sendPaymentNotification(
        Payment $payment
    ): void {
        $auth = [
            'VAPID' => [
                'subject' =>
                    config('services.webpush.subject'),

                'publicKey' =>
                    config('services.webpush.public_key'),

                'privateKey' =>
                    config('services.webpush.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        $payment->loadMissing('provider');

        $payload = json_encode([
            'title' =>
                'Nuevo ' .
                ($payment->provider?->name ?? 'pago') .
                ': S/ ' .
                number_format(
                    (float) $payment->amount,
                    2,
                    '.',
                    ''
                ),

            'body' =>
                ($payment->payer_name
                    ?: 'Cliente no identificado') .
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

            'data' => [
                'payment_id' =>
                    $payment->public_id,
            ],
        ], JSON_THROW_ON_ERROR);

        PushSubscription::query()
            ->whereHas('user', function ($query) use ($payment) {
                $query->where(
                    'business_id',
                    $payment->business_id
                );
            })
            ->each(function (
                PushSubscription $storedSubscription
            ) use ($webPush, $payload) {
                $subscription =
                    Subscription::create([
                        'endpoint' =>
                            $storedSubscription->endpoint,

                        'publicKey' =>
                            $storedSubscription->public_key,

                        'authToken' =>
                            $storedSubscription->auth_token,

                        'contentEncoding' =>
                            $storedSubscription->content_encoding,
                    ]);

                $webPush->queueNotification(
                    $subscription,
                    $payload
                );
            });

        foreach ($webPush->flush() as $report) {
            if (
                ! $report->isSuccess() &&
                $report->isSubscriptionExpired()
            ) {
                PushSubscription::query()
                    ->where(
                        'endpoint',
                        $report
                            ->getRequest()
                            ->getUri()
                            ->__toString()
                    )
                    ->delete();
            }
        }
    }
}