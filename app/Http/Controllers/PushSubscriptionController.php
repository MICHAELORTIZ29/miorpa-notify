<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        PushSubscription::updateOrCreate(
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
                    ?? $request->userAgent(),

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
        ]);
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
            ->where('user_id', $request->user()->id)
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