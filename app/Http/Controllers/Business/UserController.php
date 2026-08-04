<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreUserRequest;
use App\Http\Requests\Business\UpdateUserRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        /** @var User $administrator */
        $administrator = auth()->user();

        $users = User::query()
            ->where(
                'business_id',
                $administrator->business_id
            )
            ->where(
                'role_code',
                User::ROLE_CASHIER
            )
            ->latest()
            ->paginate(15);

        $activeUsers = User::query()
            ->where(
                'business_id',
                $administrator->business_id
            )
            ->where(
                'role_code',
                User::ROLE_CASHIER
            )
            ->where(
                'status',
                User::STATUS_ACTIVE
            )
            ->count();

        $inactiveUsers = User::query()
            ->where(
                'business_id',
                $administrator->business_id
            )
            ->where(
                'role_code',
                User::ROLE_CASHIER
            )
            ->where(
                'status',
                '!=',
                User::STATUS_ACTIVE
            )
            ->count();

        return view(
            'business.users.index',
            compact(
                'users',
                'activeUsers',
                'inactiveUsers'
            )
        );
    }

    public function create(): View
    {
        return view('business.users.create');
    }

    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        /** @var User $administrator */
        $administrator = $request->user();

        $validated = $request->validated();

        $this->ensureCashierCapacity(
            $administrator
        );

        User::query()->create([
            'business_id' =>
                $administrator->business_id,

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role_code' =>
                User::ROLE_CASHIER,

            'status' =>
                User::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('business.users.index')
            ->with(
                'success',
                'Cajero creado correctamente.'
            );
    }

    public function edit(
        User $user
    ): View {
        $this->ensureCashierBelongsToBusiness(
            $user
        );

        return view(
            'business.users.edit',
            compact('user')
        );
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->ensureCashierBelongsToBusiness(
            $user
        );

        $validated = $request->validated();

        $data = [
            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],
        ];

        if (
            !empty(
                $validated['password']
            )
        ) {
            $data['password'] =
                Hash::make(
                    $validated['password']
                );
        }

        $user->update($data);

        return redirect()
            ->route('business.users.index')
            ->with(
                'success',
                'Cajero actualizado correctamente.'
            );
    }

    public function deactivate(
        User $user
    ): RedirectResponse {
        $this->ensureCashierBelongsToBusiness(
            $user
        );

        $user->update([
            'status' =>
                User::STATUS_DISABLED,

            'disabled_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Cajero desactivado correctamente.'
        );
    }

    public function activate(
        User $user
    ): RedirectResponse {
        $this->ensureCashierBelongsToBusiness(
            $user
        );

        if (
            $user->status ===
            User::STATUS_ACTIVE
        ) {
            return back()->with(
                'info',
                'El cajero ya se encuentra activo.'
            );
        }

        /** @var User $administrator */
        $administrator = auth()->user();

        $this->ensureCashierCapacity(
            $administrator
        );

        $user->update([
            'status' =>
                User::STATUS_ACTIVE,

            'disabled_at' =>
                null,
        ]);

        return back()->with(
            'success',
            'Cajero activado correctamente.'
        );
    }

    private function ensureCashierBelongsToBusiness(
        User $user
    ): void {
        /** @var User|null $administrator */
        $administrator = auth()->user();

        abort_unless(
            $administrator !== null
            && (int) $user->business_id ===
                (int) $administrator->business_id
            && $user->isCashier(),
            404
        );
    }

    private function ensureCashierCapacity(
        User $administrator
    ): void {
        if (
            $administrator->business_id === null
        ) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    'El administrador no pertenece a un negocio.',
            ]);
        }

        $business = $administrator->business;

        if ($business === null) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    'No se encontró el negocio del administrador.',
            ]);
        }

        $subscription = $business
            ->currentSubscription()
            ->with([
                'plan.limits',
                'limitOverrides',
            ])
            ->first();

        if ($subscription === null) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    'El negocio no tiene una suscripción configurada.',
            ]);
        }

        if (
            $subscription->status ===
            Subscription::STATUS_SUSPENDED
        ) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    'La suscripción está suspendida.',
            ]);
        }

        $cashierLimit =
            (int) (
                $subscription->limit(
                    Subscription::LIMIT_CASHIERS
                ) ?? 0
            );

        if ($cashierLimit <= 0) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    'El plan no tiene cajeros habilitados.',
            ]);
        }

        $activeCashiers = User::query()
            ->where(
                'business_id',
                $administrator->business_id
            )
            ->where(
                'role_code',
                User::ROLE_CASHIER
            )
            ->where(
                'status',
                User::STATUS_ACTIVE
            )
            ->count();

        if (
            $activeCashiers >=
            $cashierLimit
        ) {
            throw ValidationException::withMessages([
                'plan_limit' =>
                    "El plan permite un máximo de {$cashierLimit} cajeros activos.",
            ]);
        }
    }
}