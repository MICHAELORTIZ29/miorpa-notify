<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Models\Device;
use App\Models\Payment;
use App\Models\PaymentProvider;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class PaymentController extends Controller
{
    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        /** @var Device $device */
        $device = $request->attributes->get('device');

        if (
            $device->type !== Device::TYPE_EMITTER
            || $device->platform !== Device::PLATFORM_ANDROID
        ) {
            return response()->json([
                'message' => 'Este dispositivo no puede enviar pagos.',
                'code' => 'DEVICE_NOT_EMITTER',
            ], 403);
        }

        $validated = $request->validated();

        $provider = PaymentProvider::query()
            ->where('code', $validated['provider_code'])
            ->where('status', PaymentProvider::STATUS_ACTIVE)
            ->first();

        if (! $provider) {
            throw ValidationException::withMessages([
                'provider_code' => [
                    'El medio de pago no existe o está desactivado.',
                ],
            ]);
        }

        $occurredAt = CarbonImmutable::parse(
            $validated['occurred_at']
        )->utc();

        if ($occurredAt->isAfter(now()->addMinutes(5))) {
            throw ValidationException::withMessages([
                'occurred_at' => [
                    'La fecha del pago no puede estar en el futuro.',
                ],
            ]);
        }

        $sourceEventHash = hash(
            'sha256',
            $provider->code . '|' . $validated['event_id']
        );

        $payment = Payment::query()->firstOrCreate(
            [
                'business_id' => $device->business_id,
                'source_event_hash' => $sourceEventHash,
            ],
            [
                'payment_provider_id' => $provider->id,
                'emitter_device_id' => $device->id,
                'external_reference' =>
                    $validated['external_reference'] ?? null,
                'payer_name' => $validated['payer_name'] ?: null,
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'status' => Payment::STATUS_RECEIVED,
                'parser_version' =>
                    $validated['parser_version'] ?? null,
                'occurred_at' => $occurredAt,
                'received_at' => now(),
                'raw_payload' =>
                    $validated['raw_payload'] ?? null,
                'metadata' => $validated['metadata'] ?? null,
            ]
        );

        $device->update([
            'last_seen_at' => now(),
            'last_ip' => $request->ip(),
            'app_version' => $device->app_version,
        ]);

        $statusCode = $payment->wasRecentlyCreated ? 201 : 200;

        return response()->json([
            'message' => $payment->wasRecentlyCreated
                ? 'Pago recibido correctamente.'
                : 'El pago ya había sido recibido.',
            'data' => [
                'payment_id' => $payment->public_id,
                'duplicate' => ! $payment->wasRecentlyCreated,
                'status' => $payment->status,
                'received_at' =>
                    $payment->received_at->toIso8601String(),
            ],
        ], $statusCode);
    }
}