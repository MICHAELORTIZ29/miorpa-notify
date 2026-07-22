<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $business = $user->business()
            ->with([
                'currentSubscription.plan',
                'currentSubscription.plan.limits',
                'currentSubscription.limitOverrides',
            ])
            ->firstOrFail();

        $timezone = $business->timezone
            ?: 'America/Lima';

        $todayStart = CarbonImmutable::now($timezone)
            ->startOfDay()
            ->utc();

        $todayEnd = CarbonImmutable::now($timezone)
            ->endOfDay()
            ->utc();

        $todayPayments = Payment::query()
            ->where('business_id', $business->id)
            ->whereBetween(
                'occurred_at',
                [$todayStart, $todayEnd]
            );

        $todayPaymentCount =
            (clone $todayPayments)->count();

        $todayPaymentTotal =
            (clone $todayPayments)->sum('amount');

        $activeEmitters = Device::query()
            ->where('business_id', $business->id)
            ->where('type', Device::TYPE_EMITTER)
            ->where('status', Device::STATUS_ACTIVE)
            ->whereNull('revoked_at')
            ->count();

        $activeReceivers = Device::query()
            ->where('business_id', $business->id)
            ->where('type', Device::TYPE_RECEIVER)
            ->where('status', Device::STATUS_ACTIVE)
            ->whereNull('revoked_at')
            ->count();

        /*
         * Consideramos conectado un dispositivo que se
         * comunicó con el servidor en los últimos 5 minutos.
         */
        $connectedDevices = Device::query()
            ->where('business_id', $business->id)
            ->where('status', Device::STATUS_ACTIVE)
            ->whereNull('revoked_at')
            ->where(
                'last_seen_at',
                '>=',
                now()->subMinutes(5)
            )
            ->count();

        $activeCashiers = User::query()
            ->where('business_id', $business->id)
            ->where('role_code', User::ROLE_CASHIER)
            ->where('status', User::STATUS_ACTIVE)
            ->count();

        $latestPayments = Payment::query()
            ->where('business_id', $business->id)
            ->with('provider')
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        $subscription = $business->currentSubscription;

        $emitterLimit = $subscription?->limit(
            Subscription::LIMIT_EMITTERS
        );

        $receiverLimit = $subscription?->limit(
            Subscription::LIMIT_RECEIVERS
        );

        $cashierLimit = $subscription?->limit(
            Subscription::LIMIT_CASHIERS
        );

        return view(
            'dashboard',
            compact(
                'business',
                'subscription',
                'todayPaymentCount',
                'todayPaymentTotal',
                'activeEmitters',
                'activeReceivers',
                'connectedDevices',
                'activeCashiers',
                'latestPayments',
                'emitterLimit',
                'receiverLimit',
                'cashierLimit'
            )
        );
    }
}