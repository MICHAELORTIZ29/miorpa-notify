<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RedeemPairingCodeRequest;
use App\Models\Device;
use App\Models\PairingCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PairingController extends Controller
{
    public function redeem(
        RedeemPairingCodeRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $result = DB::transaction(function () use (
            $request,
            $validated
        ): array {
            $pairingCode = PairingCode::query()
                ->where(
                    'code_hash',
                    PairingCode::hashPlainCode($validated['code'])
                )
                ->lockForUpdate()
                ->first();

            if (! $pairingCode || ! $pairingCode->isUsable()) {
                throw ValidationException::withMessages([
                    'code' => [
                        'El código no existe, venció o ya fue utilizado.',
                    ],
                ]);
            }

            if (
                $pairingCode->device_type === Device::TYPE_EMITTER
                && $validated['platform'] !== Device::PLATFORM_ANDROID
            ) {
                throw ValidationException::withMessages([
                    'platform' => [
                        'Los dispositivos emisores deben utilizar Android.',
                    ],
                ]);
            }

            $installationHash = hash(
                'sha256',
                $validated['installation_id']
            );

            $alreadyLinked = Device::query()
                ->where('business_id', $pairingCode->business_id)
                ->where('installation_hash', $installationHash)
                ->exists();

            if ($alreadyLinked) {
                throw ValidationException::withMessages([
                    'installation_id' => [
                        'Esta instalación ya está vinculada.',
                    ],
                ]);
            }

            // El token completo solo será devuelto en esta respuesta.
            $plainToken = bin2hex(random_bytes(32));

            $device = Device::query()->create([
                'business_id' => $pairingCode->business_id,
                'authorized_by' => $pairingCode->created_by,
                'name' => $validated['name'],
                'type' => $pairingCode->device_type,
                'platform' => $validated['platform'],
                'status' => Device::STATUS_ACTIVE,
                'token_hash' => hash('sha256', $plainToken),
                'installation_hash' => $installationHash,
                'app_version' => $validated['app_version'] ?? null,
                'last_ip' => $request->ip(),
                'user_agent' => substr(
                    (string) $request->userAgent(),
                    0,
                    500
                ),
                'capabilities' => $validated['capabilities'] ?? null,
                'authorized_at' => now(),
                'last_seen_at' => now(),
            ]);

            $pairingCode->update([
                'uses_count' => $pairingCode->uses_count + 1,
                'used_at' => now(),
                'used_by_device_id' => $device->id,
            ]);

            return [
                'device' => $device,
                'plain_token' => $plainToken,
            ];
        });

        return response()->json([
            'message' => 'Dispositivo vinculado correctamente.',
            'data' => [
                'device_id' => $result['device']->public_id,
                'business_id' => $result['device']->business->public_id,
                'device_type' => $result['device']->type,
                'token' => $result['plain_token'],
            ],
        ], 201);
    }
}