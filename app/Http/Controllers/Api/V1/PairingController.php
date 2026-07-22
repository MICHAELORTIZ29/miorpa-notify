<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RedeemPairingCodeRequest;
use App\Models\Device;
use App\Models\PairingCode;
use App\Models\Subscription;
use App\Services\SubscriptionStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PairingController extends Controller
{
    public function redeem(
        RedeemPairingCodeRequest $request,
        SubscriptionStatusService $service
    ): JsonResponse {
        $validated = $request->validated();

        $result = DB::transaction(
            function () use (
                $request,
                $validated,
                $service
            ): array {
                $pairingCode = PairingCode::query()
                    ->with('business')
                    ->where(
                        'code_hash',
                        PairingCode::hashPlainCode(
                            $validated['code']
                        )
                    )
                    ->lockForUpdate()
                    ->first();

                if (
                    ! $pairingCode ||
                    ! $pairingCode->isUsable()
                ) {
                    throw ValidationException::withMessages([
                        'code' => [
                            'El código no existe, venció o ya fue utilizado.',
                        ],
                    ]);
                }

                if (
                    $pairingCode->device_type ===
                        Device::TYPE_EMITTER
                    &&
                    $validated['platform'] !==
                        Device::PLATFORM_ANDROID
                ) {
                    throw ValidationException::withMessages([
                        'platform' => [
                            'Los dispositivos emisores deben utilizar Android.',
                        ],
                    ]);
                }

                /*
                 * Obtenemos y bloqueamos la suscripción para
                 * impedir que dos dispositivos consuman el
                 * último cupo simultáneamente.
                 */
                $subscription = $pairingCode
                    ->business
                    ->currentSubscription()
                    ->with([
                        'business',
                        'plan.limits',
                        'limitOverrides',
                    ])
                    ->lockForUpdate()
                    ->first();

                if (! $subscription) {
                    throw ValidationException::withMessages([
                        'code' => [
                            'El negocio no tiene una suscripción asignada.',
                        ],
                    ]);
                }

                /*
                 * Comprueba vencimiento y suspensión usando
                 * la fecha actual, sin depender del cron.
                 */
                $service->synchronize(
                    $subscription
                );

                $subscription->refresh();

                $pairingCode
                    ->business
                    ->refresh();

                if (
                    ! in_array(
                        $subscription->status,
                        [
                            Subscription::STATUS_TRIAL,
                            Subscription::STATUS_ACTIVE,
                            Subscription::STATUS_OVERDUE,
                        ],
                        true
                    )
                    ||
                    ! $pairingCode
                        ->business
                        ->isActive()
                ) {
                    throw ValidationException::withMessages([
                        'code' => [
                            'La suscripción del negocio no se encuentra operativa.',
                        ],
                    ]);
                }

                /*
                 * Determinamos qué límite del plan debe
                 * aplicarse según el tipo de dispositivo.
                 */
                $limitCode =
                    $pairingCode->device_type ===
                    Device::TYPE_EMITTER
                        ? Subscription::LIMIT_EMITTERS
                        : Subscription::LIMIT_RECEIVERS;

                $deviceLimit =
                    $subscription->limit(
                        $limitCode
                    ) ?? 0;

                $registeredDevices =
                    Device::query()
                        ->where(
                            'business_id',
                            $pairingCode->business_id
                        )
                        ->where(
                            'type',
                            $pairingCode->device_type
                        )
                        ->where(
                            'status',
                            '!=',
                            Device::STATUS_REVOKED
                        )
                        ->whereNull('revoked_at')
                        ->lockForUpdate()
                        ->count();

                if (
                    $registeredDevices >=
                    $deviceLimit
                ) {
                    throw ValidationException::withMessages([
                        'code' => [
                            "El negocio alcanzó el límite de {$deviceLimit} dispositivos de este tipo.",
                        ],
                    ]);
                }

                $installationHash = hash(
                    'sha256',
                    $validated['installation_id']
                );

                /*
                 * Buscamos incluso dispositivos revocados.
                 * La base tiene una restricción única por
                 * negocio e instalación.
                 */
                $existingDevice =
                    Device::query()
                        ->where(
                            'business_id',
                            $pairingCode->business_id
                        )
                        ->where(
                            'installation_hash',
                            $installationHash
                        )
                        ->lockForUpdate()
                        ->first();

                if (
                    $existingDevice &&
                    $existingDevice->status !==
                        Device::STATUS_REVOKED
                ) {
                    throw ValidationException::withMessages([
                        'installation_id' => [
                            'Esta instalación ya está vinculada.',
                        ],
                    ]);
                }

                /*
                 * El token completo solo se devuelve en
                 * esta respuesta.
                 */
                $plainToken =
                    bin2hex(random_bytes(32));

                $deviceData = [
                    'business_id' =>
                        $pairingCode->business_id,

                    'authorized_by' =>
                        $pairingCode->created_by,

                    'name' =>
                        $validated['name'],

                    'type' =>
                        $pairingCode->device_type,

                    'platform' =>
                        $validated['platform'],

                    'status' =>
                        Device::STATUS_ACTIVE,

                    'token_hash' =>
                        hash(
                            'sha256',
                            $plainToken
                        ),

                    'installation_hash' =>
                        $installationHash,

                    'app_version' =>
                        $validated['app_version']
                        ?? null,

                    'last_ip' =>
                        $request->ip(),

                    'user_agent' =>
                        substr(
                            (string)
                                $request->userAgent(),
                            0,
                            500
                        ),

                    'capabilities' =>
                        $validated['capabilities']
                        ?? null,

                    'authorized_at' => now(),
                    'last_seen_at' => now(),
                    'disabled_at' => null,
                    'revoked_at' => null,
                ];

                /*
                 * Si la misma instalación fue revocada,
                 * reutilizamos su registro. Así puede
                 * vincularse otra vez sin violar el índice
                 * único de la base de datos.
                 */
                if ($existingDevice) {
                    $existingDevice->update(
                        $deviceData
                    );

                    $device = $existingDevice;
                } else {
                    $device =
                        Device::query()->create(
                            $deviceData
                        );
                }

                $pairingCode->update([
                    'uses_count' =>
                        $pairingCode->uses_count + 1,

                    'used_at' => now(),

                    'used_by_device_id' =>
                        $device->id,
                ]);

                return [
                    'device' => $device,
                    'plain_token' => $plainToken,
                ];
            }
        );

        $result['device']->loadMissing(
            'business'
        );

        return response()->json([
            'message' =>
                'Dispositivo vinculado correctamente.',

            'data' => [
                'device_id' =>
                    $result['device']->public_id,

                'business_id' =>
                    $result['device']
                        ->business
                        ->public_id,

                'device_type' =>
                    $result['device']->type,

                'token' =>
                    $result['plain_token'],
            ],
        ], 201);
    }
}