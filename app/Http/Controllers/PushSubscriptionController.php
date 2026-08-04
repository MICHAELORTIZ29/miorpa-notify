<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PushSubscription;
use App\Services\WebPushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => [
                'required',
                'string',
                'max:2048',
                'url',
            ],

            'keys.p256dh' => [
                'required',
                'string',
            ],

            'keys.auth' => [
                'required',
                'string',
            ],

            'contentEncoding' => [
                'nullable',
                'string',
                'max:50',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'platform' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $subscription = PushSubscription::updateOrCreate(
            [
                'endpoint' =>
                    $validated['endpoint'],
            ],
            [
                'user_id' =>
                    $request->user()->id,

                'public_key' =>
                    $validated['keys']['p256dh'],

                'auth_token' =>
                    $validated['keys']['auth'],

                'content_encoding' =>
                    $validated['contentEncoding']
                    ?? 'aes128gcm',

                'device_name' =>
                    $validated['device_name']
                    ?? mb_substr(
                        (string) $request->userAgent(),
                        0,
                        255
                    ),

                'platform' =>
                    $validated['platform']
                    ?? 'web',

                'last_used_at' =>
                    now(),
            ]
        );

        return response()->json([
            'message' =>
                'Dispositivo preparado para recibir notificaciones.',

            'subscription_id' =>
                $subscription->id,
        ]);
    }

    public function test(
        Request $request,
        WebPushNotificationService $webPushService
    ): JsonResponse {
        try {
            $user = $request->user();

            $payment = Payment::query()
                ->where(
                    'business_id',
                    $user->business_id
                )
                ->latest('id')
                ->first();

            if (! $payment) {
                return response()->json([
                    'message' =>
                        'El negocio todavía no tiene pagos.',
                ], 404);
            }

            $result =
                $webPushService
                    ->sendPaymentNotification(
                        $payment
                    );

            return response()->json([
                'message' =>
                    'Prueba Web Push ejecutada.',

                'data' =>
                    $result,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' =>
                    'No se pudo ejecutar la prueba Web Push.',

                'exception' =>
                    get_class($exception),

                'error' =>
                    $exception->getMessage(),

                'file' =>
                    basename($exception->getFile()),

                'line' =>
                    $exception->getLine(),
            ], 500);
        }
    }

    public function destroy(
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'endpoint' => [
                'required',
                'string',
                'max:2048',
                'url',
            ],
        ]);

        PushSubscription::query()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->where(
                'endpoint',
                $validated['endpoint']
            )
            ->delete();

        return response()->json([
            'message' =>
                'Dispositivo eliminado correctamente.',
        ]);
    }
}