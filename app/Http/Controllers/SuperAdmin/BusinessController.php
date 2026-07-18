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

class BusinessController extends Controller
{
    public function index(): View
{
    $businesses = Business::query()
        ->with([
            'users' => fn ($query) => $query
                ->where('role_code', User::ROLE_ADMINISTRATOR)
                ->orderBy('name'),
        ])
        ->withCount('users')
        ->latest()
        ->paginate(15);

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

    return view('superadmin.businesses.index', compact(
        'businesses',
        'totalBusinesses',
        'activeBusinesses',
        'trialBusinesses',
        'suspendedBusinesses'
    ));
}

    public function create(): View
    {
        return view('superadmin.businesses.create');
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $business = DB::transaction(function () use ($validated): Business {
            $business = Business::query()->create([
                'name' => $validated['name'],
                'legal_name' => $validated['legal_name'] ?? null,
                'tax_id' => $validated['tax_id'] ?? null,
                'timezone' => 'America/Lima',
                'status' => Business::STATUS_ACTIVE,
                'contact_phone' => $validated['contact_phone'] ?? null,
            ]);

            User::query()->create([
                'business_id' => $business->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role_code' => User::ROLE_ADMINISTRATOR,
                'status' => User::STATUS_ACTIVE,
            ]);

            return $business;
        });

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('success', 'Negocio y administrador creados correctamente.');
    }

    public function show(Business $business): View
    {
        $business->load([
            'users' => fn ($query) => $query
                ->orderBy('role_code')
                ->orderBy('name'),
        ]);

        return view('superadmin.businesses.show', compact('business'));
    }

    public function edit(Business $business): View
{
    $administrator = $business->users()
        ->where('role_code', User::ROLE_ADMINISTRATOR)
        ->oldest('id')
        ->firstOrFail();

    return view(
        'superadmin.businesses.edit',
        compact('business', 'administrator')
    );
}

public function update(
    UpdateBusinessRequest $request,
    Business $business
): RedirectResponse {
    $validated = $request->validated();

    DB::transaction(function () use ($business, $validated): void {
        $business->update([
            'name' => $validated['name'],
            'legal_name' => $validated['legal_name'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'timezone' => $validated['timezone'],
        ]);

        $administrator = $business->users()
            ->where('role_code', User::ROLE_ADMINISTRATOR)
            ->oldest('id')
            ->firstOrFail();

        $administratorData = [
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
        ];

        if (! empty($validated['admin_password'])) {
            $administratorData['password'] = Hash::make(
                $validated['admin_password']
            );
        }

        $administrator->update($administratorData);
    });

    return redirect()
        ->route('superadmin.businesses.show', $business)
        ->with('success', 'El negocio y su administrador fueron actualizados.');
}


    public function suspend(Business $business): RedirectResponse
    {
        if ($business->status === Business::STATUS_SUSPENDED) {
            return back()->with('info', 'El negocio ya se encuentra suspendido.');
        }

        $business->update([
            'status' => Business::STATUS_SUSPENDED,
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
            'status' => Business::STATUS_ACTIVE,
            'suspended_at' => null,
            'closed_at' => null,
        ]);

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('success', 'Negocio activado correctamente.');
    }
}