<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreBusinessRequest;
use App\Http\Requests\SuperAdmin\UpdateBusinessRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;




class BusinessController extends Controller
{
public function destroy(
    Request $request,
    Business $business
): RedirectResponse {
    $request->validate([
        'confirmation_tax_id' => [
            'required',
            'string',
            'max:30',
        ],
    ], [
        'confirmation_tax_id.required' =>
            'Debes ingresar el RUC para confirmar.',
    ]);

    $enteredTaxId = trim(
        (string) $request->input('confirmation_tax_id')
    );

    $businessTaxId = trim(
        (string) $business->tax_id
    );

    if (
        $businessTaxId === ''
        || ! hash_equals(
            $businessTaxId,
            $enteredTaxId
        )
    ) {
        return back()
            ->withErrors([
                'confirmation_tax_id' =>
                    'El RUC no coincide con el negocio.',
            ])
            ->withInput();
    }

    $businessName = $business->name;

    DB::transaction(function () use ($business): void {
        $paymentIds = DB::table('payments')
            ->where('business_id', $business->id)
            ->pluck('id');

        if ($paymentIds->isNotEmpty()) {
            DB::table('payment_acknowledgements')
                ->whereIn('payment_id', $paymentIds)
                ->delete();
        }

        DB::table('payments')
            ->where('business_id', $business->id)
            ->delete();

        DB::table('pairing_codes')
            ->where('business_id', $business->id)
            ->delete();

        DB::table('devices')
            ->where('business_id', $business->id)
            ->delete();

        $subscriptionIds = DB::table('subscriptions')
            ->where('business_id', $business->id)
            ->pluck('id');

        if ($subscriptionIds->isNotEmpty()) {
            DB::table('subscription_limit_overrides')
                ->whereIn(
                    'subscription_id',
                    $subscriptionIds
                )
                ->delete();
        }

        DB::table('subscriptions')
            ->where('business_id', $business->id)
            ->delete();

        DB::table('users')
            ->where('business_id', $business->id)
            ->delete();

        $business->delete();
    });

    return redirect()
        ->route('superadmin.businesses.index')
        ->with(
            'success',
            "El negocio {$businessName} y todos sus datos fueron eliminados."
        );
}
public function index(Request $request): View
{
    $search = trim(
        (string) $request->query('search', '')
    );

    $status = (string) $request->query('status', '');

    $allowedStatuses = [
        Business::STATUS_TRIAL,
        Business::STATUS_ACTIVE,
        Business::STATUS_OVERDUE,
        Business::STATUS_SUSPENDED,
        Business::STATUS_CLOSED,
    ];

    if (! in_array($status, $allowedStatuses, true)) {
        $status = '';
    }

    $businesses = Business::query()
        ->with([
            'users' => fn ($query) => $query
                ->where(
                    'role_code',
                    User::ROLE_ADMINISTRATOR
                )
                ->orderBy('name'),

            'currentSubscription.plan',
        ])
        ->withCount('users')
        ->when(
            $search !== '',
            function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('legal_name', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%")
                        ->orWhereHas(
                            'users',
                            function ($query) use ($search): void {
                                $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                });
            }
        )
        ->when(
            $status !== '',
            fn ($query) => $query->where(
                'status',
                $status
            )
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $totalBusinesses = Business::query()->count();

    $activeBusinesses = Business::query()
        ->where('status', Business::STATUS_ACTIVE)
        ->count();

    $trialBusinesses = Business::query()
        ->where('status', Business::STATUS_TRIAL)
        ->count();

    $suspendedBusinesses = Business::query()
        ->where('status', Business::STATUS_SUSPENDED)
        ->count();

    return view(
        'superadmin.businesses.index',
        compact(
            'businesses',
            'totalBusinesses',
            'activeBusinesses',
            'trialBusinesses',
            'suspendedBusinesses',
            'search',
            'status'
        )
    );
}

    public function create(): View
    {
        $plans = Plan::query()
            ->where(
                'status',
                Plan::STATUS_ACTIVE
            )
            ->with('limits')
            ->orderBy('name')
            ->get();

        return view(
            'superadmin.businesses.create',
            compact('plans')
        );
    }

    public function store(
        StoreBusinessRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        $business = DB::transaction(
            function () use ($validated): Business {
                $business = Business::query()->create([
                    'name' => $validated['name'],

                    'legal_name' =>
                        $validated['legal_name'] ?? null,

                    'tax_id' =>
                        $validated['tax_id'] ?? null,

                    'timezone' => 'America/Lima',

                    'status' =>
                        Business::STATUS_ACTIVE,

                    'contact_name' =>
                        $validated['admin_name'],

                    'contact_email' =>
                        $validated['admin_email'],

                    'contact_phone' =>
                        $validated['contact_phone']
                        ?? null,
                ]);

                User::query()->create([
                    'business_id' => $business->id,

                    'name' =>
                        $validated['admin_name'],

                    'email' =>
                        $validated['admin_email'],

                    'phone' =>
                        $validated['admin_phone']
                        ?? null,

                    'password' => Hash::make(
                        $validated['admin_password']
                    ),

                    'role_code' =>
                        User::ROLE_ADMINISTRATOR,

                    'status' =>
                        User::STATUS_ACTIVE,
                ]);

                $this->createSubscription(
                    $business,
                    $validated
                );

                return $business;
            }
        );

        return redirect()
            ->route(
                'superadmin.businesses.show',
                $business
            )
            ->with(
                'success',
                'Negocio, administrador y suscripción creados correctamente.'
            );
    }

    public function show(
        Business $business
    ): View {
        $business->load([
            'users' => fn($query) => $query
                ->orderBy('role_code')
                ->orderBy('name'),

            'currentSubscription.plan.limits',

            'currentSubscription.limitOverrides',
        ]);

        return view(
            'superadmin.businesses.show',
            compact('business')
        );
    }

   public function edit(
    Business $business
): View {
    $administrator = $business->users()
        ->where(
            'role_code',
            User::ROLE_ADMINISTRATOR
        )
        ->oldest('id')
        ->firstOrFail();

    $business->load([
        'currentSubscription.plan.limits',
        'currentSubscription.limitOverrides',
    ]);

    $plans = Plan::query()
        ->where(
            'status',
            Plan::STATUS_ACTIVE
        )
        ->with('limits')
        ->orderBy('name')
        ->get();

    return view(
        'superadmin.businesses.edit',
        compact(
            'business',
            'administrator',
            'plans'
        )
    );
}

 public function update(
    UpdateBusinessRequest $request,
    Business $business
): RedirectResponse {
    $validated = $request->validated();

    DB::transaction(
        function () use (
            $business,
            $validated
        ): void {
            $business->update([
                'name' =>
                    $validated['name'],

                'legal_name' =>
                    $validated['legal_name']
                    ?? null,

                'tax_id' =>
                    $validated['tax_id']
                    ?? null,

                'contact_name' =>
                    $validated['admin_name'],

                'contact_email' =>
                    $validated['admin_email'],

                'contact_phone' =>
                    $validated['contact_phone']
                    ?? null,

                'timezone' =>
                    $validated['timezone'],
            ]);

            $administrator = $business->users()
                ->where(
                    'role_code',
                    User::ROLE_ADMINISTRATOR
                )
                ->oldest('id')
                ->firstOrFail();

            $administratorData = [
                'name' =>
                    $validated['admin_name'],

                'email' =>
                    $validated['admin_email'],
            ];

            if (
                ! empty(
                    $validated['admin_password']
                )
            ) {
                $administratorData['password'] =
                    Hash::make(
                        $validated['admin_password']
                    );
            }

            $administrator->update(
                $administratorData
            );

            $this->updateSubscription(
                $business,
                $validated
            );
        }
    );

    return redirect()
        ->route(
            'superadmin.businesses.show',
            $business
        )
        ->with(
            'success',
            'Negocio, administrador y suscripción actualizados correctamente.'
        );
}


    public function suspend(Business $business): RedirectResponse
    {
        if ($business->status === Business::STATUS_SUSPENDED) {
            return back()->with('info', 'El negocio ya se encuentra suspendido.');
        }

        $business->update([
    'status' =>
        Business::STATUS_SUSPENDED,

    'suspension_reason' =>
        Business::SUSPENSION_MANUAL,

    'suspended_at' => now(),
]);

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('success', 'Negocio suspendido correctamente.');
    }

    public function activate(Business $business): RedirectResponse
    {
        if ($business->status === Business::STATUS_ACTIVE) {
            return back()->with('info', 'El negocio ya se encuentra activo.');
        }

        $business->update([
    'status' =>
        Business::STATUS_ACTIVE,

    'suspension_reason' => null,
    'suspended_at' => null,
    'closed_at' => null,
]);

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('success', 'Negocio activado correctamente.');
    }
    private function createSubscription(
        Business $business,
        array $validated
    ): Subscription {
        $startsAt = CarbonImmutable::parse(
            $validated['subscription_starts_at'],
            'America/Lima'
        )
            ->startOfDay()
            ->utc();

        $periodEndsAt = CarbonImmutable::parse(
            $validated['subscription_ends_at'],
            'America/Lima'
        )
            ->endOfDay()
            ->utc();

        $graceEndsAt = $periodEndsAt->addDays(
            (int) $validated['grace_days']
        );

        $subscription = Subscription::query()->create([
            'business_id' => $business->id,

            'plan_id' =>
                $validated['plan_id'],

            'billing_cycle' =>
                $validated['billing_cycle'],

            'status' =>
                Subscription::STATUS_ACTIVE,

            'price' =>
                $validated['subscription_price']
                ?? null,

            'currency' => 'PEN',

            'starts_at' => $startsAt,

            'current_period_ends_at' =>
                $periodEndsAt,

            'grace_ends_at' =>
                $graceEndsAt,

            'auto_suspend' => (bool) (
                $validated['auto_suspend']
                ?? false
            ),

            'terms_snapshot_json' => [
                'billing_cycle' =>
                    $validated['billing_cycle'],

                'price' =>
                    $validated['subscription_price']
                    ?? null,

                'grace_days' =>
                    (int) $validated['grace_days'],
            ],
        ]);

        $limits = [
            Subscription::LIMIT_EMITTERS =>
                $validated['max_emitters'],

            Subscription::LIMIT_RECEIVERS =>
                $validated['max_receivers'],

            Subscription::LIMIT_CASHIERS =>
                $validated['max_cashiers'],
        ];

        foreach ($limits as $limitCode => $value) {
            $subscription
                ->limitOverrides()
                ->create([
                    'limit_code' => $limitCode,
                    'numeric_value' => $value,
                ]);
        }

        return $subscription;
    }
    private function updateSubscription(
    Business $business,
    array $validated
): Subscription {
    $startsAt = CarbonImmutable::parse(
        $validated['subscription_starts_at'],
        'America/Lima'
    )
        ->startOfDay()
        ->utc();

    $periodEndsAt = CarbonImmutable::parse(
        $validated['subscription_ends_at'],
        'America/Lima'
    )
        ->endOfDay()
        ->utc();

    $graceEndsAt = $periodEndsAt->addDays(
        (int) $validated['grace_days']
    );

    $subscription = $business
        ->currentSubscription()
        ->firstOrFail();

    $subscription->update([
        'plan_id' =>
            $validated['plan_id'],

        'billing_cycle' =>
            $validated['billing_cycle'],

        'price' =>
            $validated['subscription_price']
            ?? null,

        'currency' => 'PEN',

        'starts_at' =>
            $startsAt,

        'current_period_ends_at' =>
            $periodEndsAt,

        'grace_ends_at' =>
            $graceEndsAt,

        'auto_suspend' => (bool) (
            $validated['auto_suspend']
            ?? false
        ),

        'terms_snapshot_json' => [
            'billing_cycle' =>
                $validated['billing_cycle'],

            'price' =>
                $validated['subscription_price']
                ?? null,

            'grace_days' =>
                (int) $validated['grace_days'],
        ],
    ]);

    $limits = [
        Subscription::LIMIT_EMITTERS =>
            $validated['max_emitters'],

        Subscription::LIMIT_RECEIVERS =>
            $validated['max_receivers'],

        Subscription::LIMIT_CASHIERS =>
            $validated['max_cashiers'],
    ];

    foreach ($limits as $limitCode => $value) {
        $subscription
            ->limitOverrides()
            ->updateOrCreate(
                [
                    'limit_code' => $limitCode,
                ],
                [
                    'numeric_value' => $value,
                ]
            );
    }

    return $subscription->refresh();
}
}