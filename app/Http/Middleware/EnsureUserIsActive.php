<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Models\User;
use App\Services\SubscriptionStatusService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function __construct(
        private readonly SubscriptionStatusService $service
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        /*
         * Primero comprobamos el estado personal
         * del usuario.
         */
        if (! $user->isActive()) {
            return $this->closeSession(
                $request
            );
        }

        /*
         * El superadministrador no pertenece a una
         * suscripción de negocio.
         */
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $business = $user->business;

        if ($business === null) {
            return $this->closeSession(
                $request
            );
        }

        $subscription = $business
            ->currentSubscription()
            ->with('business')
            ->first();

        /*
         * Sincronizamos en tiempo real. Si la fecha
         * acaba de vencer, el cambio se aplica en esta
         * misma solicitud.
         */
        if ($subscription !== null) {
            $this->service->synchronize(
                $subscription
            );

            $business->refresh();
        }

        $suspendedRouteAllowed =
            $request->routeIs(
                'business.subscription-suspended',
                'logout'
            );

        /*
         * Un negocio sin suscripción tampoco puede
         * continuar operando.
         */
        if (
            $subscription === null ||
            $business->status ===
                Business::STATUS_SUSPENDED
        ) {
            if ($suspendedRouteAllowed) {
                return $next($request);
            }

            return redirect()->route(
                'business.subscription-suspended'
            );
        }

        if (! $business->isActive()) {
            return $this->closeSession(
                $request
            );
        }

        return $next($request);
    }

    private function closeSession(
        Request $request
    ): Response {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()
            ->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' =>
                    'Tu acceso ya no se encuentra disponible.',
            ]);
    }
}