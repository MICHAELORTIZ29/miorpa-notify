<?php

namespace App\Http\Controllers\Receiver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receiver\ReceiverLinkRequest;
use App\Models\Device;
use App\Models\PairingCode;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReceiverLinkController extends Controller
{
    public function create(): View
    {
        return view('receiver.link');
    }

    public function store(
        ReceiverLinkRequest $request
    ): RedirectResponse {
        $validated = $request->validated();
        $user = $request->user();

        $plainToken = bin2hex(
            random_bytes(32)
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $user,
            $plainToken
        ): void {
            $pairingCode = PairingCode::query()
                ->where(
                    'code_hash',
                    PairingCode::hashPlainCode(
                        $validated['code']
                    )
                )
                ->lockForUpdate()
                ->first();

            if (
                $pairingCode === null
                || ! $pairingCode->isUsable()
            ) {
                throw ValidationException::withMessages([
                    'code' =>
                        'El código no existe, venció o ya fue utilizado.',
                ]);
            }

            if (
                $pairingCode->business_id !==
                $user->business_id
            ) {
                throw ValidationException::withMessages([
                    'code' =>
                        'Este código pertenece a otro negocio.',
                ]);
            }

            if (
                $pairingCode->device_type !==
                Device::TYPE_RECEIVER
            ) {
                throw ValidationException::withMessages([
                    'code' =>
                        'Este código no autoriza un receptor web.',
                ]);
            }

            $subscription = $user
                ->business
                ->currentSubscription()
                ->with([
                    'plan.limits',
                    'limitOverrides',
                ])
                ->lockForUpdate()
                ->first();

            if ($subscription === null) {
                throw ValidationException::withMessages([
                    'code' =>
                        'El negocio no tiene una suscripción configurada.',
                ]);
            }

            if (
                $subscription->status ===
                Subscription::STATUS_SUSPENDED
            ) {
                throw ValidationException::withMessages([
                    'code' =>
                        'La suscripción se encuentra suspendida.',
                ]);
            }

            $installationHash = hash(
                'sha256',
                $validated['installation_id']
            );

            $device = Device::query()
                ->where(
                    'business_id',
                    $user->business_id
                )
                ->where(
                    'installation_hash',
                    $installationHash
                )
                ->lockForUpdate()
                ->first();

            if (
                $device !== null
                && $device->status ===
                    Device::STATUS_REVOKED
            ) {
                throw ValidationException::withMessages([
                    'code' =>
                        'Este navegador fue revocado. El administrador debe autorizarlo nuevamente.',
                ]);
            }

            if ($device === null) {
                $receiverLimit = $subscription->limit(
                    Subscription::LIMIT_RECEIVERS
                ) ?? 0;

                $registeredReceivers = Device::query()
                    ->where(
                        'business_id',
                        $user->business_id
                    )
                    ->where(
                        'type',
                        Device::TYPE_RECEIVER
                    )
                    ->where(
                        'status',
                        '!=',
                        Device::STATUS_REVOKED
                    )
                    ->count();

                if (
                    $registeredReceivers >=
                    $receiverLimit
                ) {
                    throw ValidationException::withMessages([
                        'code' =>
                            "El plan permite un máximo de {$receiverLimit} dispositivos receptores.",
                    ]);
                }

                $device = new Device([
                    'business_id' =>
                        $user->business_id,

                    'authorized_by' =>
                        $pairingCode->created_by,

                    'installation_hash' =>
                        $installationHash,

                    'type' =>
                        Device::TYPE_RECEIVER,

                    'platform' =>
                        Device::PLATFORM_WEB,
                ]);
            }

            $device->fill([
                'name' =>
                    $validated['device_name'],

                'status' =>
                    Device::STATUS_ACTIVE,

                'token_hash' =>
                    hash('sha256', $plainToken),

                'last_ip' =>
                    $request->ip(),

                'user_agent' => substr(
                    (string) $request->userAgent(),
                    0,
                    500
                ),

                'capabilities' => [
                    'browser_receiver' => true,
                    'web_notifications' => true,
                ],

                'authorized_at' =>
                    $device->authorized_at
                    ?? now(),

                'last_seen_at' =>
                    now(),

                'disabled_at' =>
                    null,
            ]);

            $device->save();

            $pairingCode->update([
                'uses_count' =>
                    $pairingCode->uses_count + 1,

                'used_at' =>
                    now(),

                'used_by_device_id' =>
                    $device->id,
            ]);
        });

        $cookie = Cookie::make(
            'miorpa_receiver_token',
            $plainToken,
            60 * 24 * 365,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax'
        );

        return redirect()
            ->route('business.payments.index')
            ->with(
                'success',
                'Este navegador fue vinculado como receptor.'
            )
            ->withCookie($cookie);
    }
}