<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StorePairingCodeRequest;
use App\Models\Device;
use App\Models\PairingCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(): View
    {
        $administrator = auth()->user();

        $devices = Device::query()
            ->where('business_id', $administrator->business_id)
            ->latest()
            ->paginate(15);

        $pairingCodes = PairingCode::query()
            ->where('business_id', $administrator->business_id)
            ->latest()
            ->limit(10)
            ->get();

        $activeDevices = Device::query()
            ->where('business_id', $administrator->business_id)
            ->where('status', Device::STATUS_ACTIVE)
            ->count();

        $emitterDevices = Device::query()
            ->where('business_id', $administrator->business_id)
            ->where('type', Device::TYPE_EMITTER)
            ->where('status', Device::STATUS_ACTIVE)
            ->count();

        $receiverDevices = Device::query()
            ->where('business_id', $administrator->business_id)
            ->where('type', Device::TYPE_RECEIVER)
            ->where('status', Device::STATUS_ACTIVE)
            ->count();

        return view('business.devices.index', compact(
            'devices',
            'pairingCodes',
            'activeDevices',
            'emitterDevices',
            'receiverDevices'
        ));
    }

    public function storePairingCode(
        StorePairingCodeRequest $request
    ): RedirectResponse {
        $administrator = $request->user();
        $validated = $request->validated();

        $result = PairingCode::issue(
            $administrator->business,
            $administrator,
            $validated['device_type'],
            (int) $validated['valid_minutes']
        );

        return redirect()
            ->route('business.devices.index')
            ->with('success', 'Código de vinculación generado.')
            ->with('new_pairing_code', $result['plain_code'])
            ->with(
                'pairing_code_expires_at',
                $result['pairing_code']->expires_at
                    ->timezone('America/Lima')
                    ->format('d/m/Y H:i')
            );
    }

    public function revokePairingCode(
        PairingCode $pairingCode
    ): RedirectResponse {
        $this->ensurePairingCodeBelongsToBusiness($pairingCode);

        if ($pairingCode->used_at !== null) {
            return back()->with(
                'info',
                'El código ya fue utilizado y no puede revocarse.'
            );
        }

        $pairingCode->update([
            'revoked_at' => now(),
        ]);

        return back()->with(
            'success',
            'Código de vinculación revocado.'
        );
    }

    public function deactivate(Device $device): RedirectResponse
    {
        $this->ensureDeviceBelongsToBusiness($device);

        abort_if(
            $device->status === Device::STATUS_REVOKED,
            422,
            'El dispositivo ya fue revocado.'
        );

        $device->update([
            'status' => Device::STATUS_DISABLED,
            'disabled_at' => now(),
        ]);

        return back()->with('success', 'Dispositivo desactivado.');
    }

    public function activate(Device $device): RedirectResponse
    {
        $this->ensureDeviceBelongsToBusiness($device);

        abort_if(
            $device->status === Device::STATUS_REVOKED,
            422,
            'Un dispositivo revocado no puede reactivarse.'
        );

        $device->update([
            'status' => Device::STATUS_ACTIVE,
            'disabled_at' => null,
        ]);

        return back()->with('success', 'Dispositivo activado.');
    }

    public function revoke(Device $device): RedirectResponse
    {
        $this->ensureDeviceBelongsToBusiness($device);

        $device->update([
            'status' => Device::STATUS_REVOKED,
            'token_hash' => null,
            'revoked_at' => now(),
            'disabled_at' => now(),
        ]);

        return back()->with(
            'success',
            'El dispositivo fue desvinculado permanentemente.'
        );
    }

    private function ensureDeviceBelongsToBusiness(Device $device): void
    {
        abort_unless(
            $device->business_id === auth()->user()->business_id,
            404
        );
    }

    private function ensurePairingCodeBelongsToBusiness(
        PairingCode $pairingCode
    ): void {
        abort_unless(
            $pairingCode->business_id === auth()->user()->business_id,
            404
        );
    }
}