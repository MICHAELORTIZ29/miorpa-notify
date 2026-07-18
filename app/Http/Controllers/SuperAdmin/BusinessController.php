<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreBusinessRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(): View
    {
        $businesses = Business::query()
            ->withCount('users')
            ->with([
                'users' => fn ($query) => $query
                    ->where('role_code', User::ROLE_ADMINISTRATOR)
                    ->orderBy('id'),
            ])
            ->latest()
            ->paginate(15);

        return view('superadmin.businesses.index', [
            'businesses' => $businesses,
            'activeBusinesses' => Business::query()
                ->where('status', Business::STATUS_ACTIVE)
                ->count(),
            'trialBusinesses' => Business::query()
                ->where('status', Business::STATUS_TRIAL)
                ->count(),
        ]);
    }

    public function create(): View
    {
        return view('superadmin.businesses.create');
    }

    public function store(
        StoreBusinessRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $business = DB::transaction(function () use ($data): Business {
            $business = Business::create([
                'name' => $data['name'],
                'legal_name' => $data['legal_name'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'timezone' => 'America/Lima',
                'status' => Business::STATUS_ACTIVE,
                'contact_name' => $data['contact_name'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
            ]);

            User::create([
                'business_id' => $business->id,
                'role_code' => User::ROLE_ADMINISTRATOR,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'phone' => $data['admin_phone'] ?? null,
                'password' => $data['admin_password'],
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'password_changed_at' => now(),
                'created_by_user_id' => auth()->id(),
            ]);

            return $business;
        });

        return redirect()
            ->route('superadmin.businesses.show', $business)
            ->with('success', 'Negocio creado correctamente.');
    }

    public function show(Business $business): View
    {
        $business->load([
            'users' => fn ($query) => $query
                ->orderBy('role_code')
                ->orderBy('name'),
        ]);

        return view('superadmin.businesses.show', [
            'business' => $business,
        ]);
    }
}