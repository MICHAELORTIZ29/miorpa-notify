<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreUserRequest;
use App\Http\Requests\Business\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\Subscription;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): View
    {
        $administrator = auth()->user();

        $users = User::query()
            ->where('business_id', $administrator->business_id)
            ->where('role_code', User::ROLE_CASHIER)
            ->latest()
            ->paginate(15);

        $activeUsers = User::query()
            ->where('business_id', $administrator->business_id)
            ->where('role_code', User::ROLE_CASHIER)
            ->where('status', User::STATUS_ACTIVE)
            ->count();

        $inactiveUsers = User::query()
            ->where('business_id', $administrator->business_id)
            ->where('role_code', User::ROLE_CASHIER)
            ->where('status', '!=', User::STATUS_ACTIVE)
            ->count();

        return view('business.users.index', compact(
            'users',
            'activeUsers',
            'inactiveUsers'
        ));
    }

    public function create(): View
    {
        return view('business.users.create');
    }

   public function store(
    StoreUserRequest $request
): RedirectResponse {
    $administrator = $request->user();
    $validated = $request->validated();

    $this->ensureCashierCapacity(
        $administrator->business_id
    );

    User::query()->create([
        'business_id' =>
            $administrator->business_id,

        'name' =>
            $validated['name'],

        'email' =>
            $validated['email'],

        'password' => Hash::make(
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
    public function edit(User $user): View
    {
        $this->ensureCashierBelongsToBusiness($user);

        return view('business.users.edit', compact('user'));
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $this->ensureCashierBelongsToBusiness($user);

        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('business.users.index')
            ->with('success', 'Cajero actualizado correctamente.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $this->ensureCashierBelongsToBusiness($user);

        $user->update([
            'status' => User::STATUS_DISABLED,
            'disabled_at' => now(),
        ]);

        return back()->with('success', 'Cajero desactivado correctamente.');
    }

    public function activate(
    User $user
): RedirectResponse {
    $this->ensureCashierBelongsToBusiness(
        $user
    );

    if ($user->status === User::STATUS_ACTIVE) {
        return back()->with(
            'info',
            'El cajero ya se encuentra activo.'
        );
    }

    $this->ensureCashierCapacity(
        $user->business_id
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

    private function ensureCashierBelongsToBusiness(User $user): void
    {
        $administrator = auth()->user();

        abort_unless(
            $user->business_id === $administrator->business_id
            && $user->isCashier(),
            404
        );
    }
    private function ensureCashierCapacity(
    int $businessId
): void {
    $administrator = auth()->user();

    abort_unless(
        $administrator !== null
        && $administrator->business_id === $businessId,
        404
    );

    $subscription = $administrator
        ->business
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

    $cashierLimit = $subscription->limit(
        Subscription::LIMIT_CASHIERS
    ) ?? 0;

    $activeCashiers = User::query()
        ->where(
            'business_id',
            $businessId
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

    if ($activeCashiers >= $cashierLimit) {
        throw ValidationException::withMessages([
            'plan_limit' =>
                "El plan permite un máximo de {$cashierLimit} cajeros activos.",
        ]);
    }
}
}