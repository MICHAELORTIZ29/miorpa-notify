<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureReceiverDeviceIsLinked
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user || ! $user->business_id) {
            return redirect()->route('login');
        }

        /*
         * Esta cookie pertenece al navegador o tablet,
         * no al usuario que haya iniciado sesión.
         */
        $plainToken = $request->cookie(
            'miorpa_receiver_token'
        );

        if (! is_string($plainToken) || $plainToken === '') {
            return redirect()
                ->route('receiver.link.create')
                ->with(
                    'warning',
                    'Este dispositivo todavía no está vinculado como receptor.'
                );
        }

        /*
         * La vinculación es válida para cualquier usuario
         * activo que pertenezca al mismo negocio.
         */
        $device = Device::query()
            ->where('business_id', $user->business_id)
            ->where('type', Device::TYPE_RECEIVER)
            ->where('platform', Device::PLATFORM_WEB)
            ->where('status', Device::STATUS_ACTIVE)
            ->whereNull('revoked_at')
            ->where(
                'token_hash',
                hash('sha256', $plainToken)
            )
            ->first();

        if (! $device) {
            return redirect()
                ->route('receiver.link.create')
                ->withCookie(
                    cookie()->forget(
                        'miorpa_receiver_token'
                    )
                )
                ->with(
                    'warning',
                    'La vinculación no existe, fue desactivada o pertenece a otro negocio.'
                );
        }

        if (
            $device->last_seen_at === null ||
            $device->last_seen_at->lt(now()->subMinute())
        ) {
            $device->forceFill([
                'last_seen_at' => now(),
                'last_ip' => $request->ip(),
                'user_agent' => mb_substr(
                    (string) $request->userAgent(),
                    0,
                    1000
                ),
            ])->save();
        }

        /*
         * Así podremos registrar desde qué tablet
         * se verificó cada pago.
         */
        $request->attributes->set(
            'receiver_device',
            $device
        );

        return $next($request);
    }
}