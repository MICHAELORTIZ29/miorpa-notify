<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\PaymentIndexRequest;
use App\Models\Payment;
use App\Models\PaymentAcknowledgement;
use App\Models\PaymentProvider;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(PaymentIndexRequest $request): View
    {
        $user = $request->user();
        $filters = $request->validated();

        $query = Payment::query()
            ->where('business_id', $user->business_id)
            ->with(['provider', 'emitterDevice'])
            ->withExists([
                'acknowledgements as confirmed_by_user' => fn ($query) =>
                    $query
                        ->where('user_id', $user->id)
                        ->whereNotNull('confirmed_at'),
            ])
            ->latest('occurred_at');

        $query
            ->when(
                $filters['search'] ?? null,
                fn ($query, $search) => $query->where(
                    fn ($query) => $query
                        ->where('payer_name', 'like', "%{$search}%")
                        ->orWhere(
                            'external_reference',
                            'like',
                            "%{$search}%"
                        )
                )
            )
            ->when(
                $filters['provider'] ?? null,
                fn ($query, $provider) => $query->whereHas(
                    'provider',
                    fn ($query) => $query->where('code', $provider)
                )
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) =>
                    $query->where('status', $status)
            )
            ->when(
                $filters['amount'] ?? null,
                fn ($query, $amount) =>
                    $query->where('amount', $amount)
            )
            ->when(
                $filters['date_from'] ?? null,
                fn ($query, $date) => $query->where(
                    'occurred_at',
                    '>=',
                    CarbonImmutable::parse(
                        $date,
                        'America/Lima'
                    )->startOfDay()->utc()
                )
            )
            ->when(
                $filters['date_to'] ?? null,
                fn ($query, $date) => $query->where(
                    'occurred_at',
                    '<=',
                    CarbonImmutable::parse(
                        $date,
                        'America/Lima'
                    )->endOfDay()->utc()
                )
            );

        $payments = $query
            ->paginate(30)
            ->withQueryString();

        $todayStart = now('America/Lima')
            ->startOfDay()
            ->utc();

        $todayEnd = now('America/Lima')
            ->endOfDay()
            ->utc();

        $todayQuery = Payment::query()
            ->where('business_id', $user->business_id)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd]);

        $todayPaymentCount = (clone $todayQuery)->count();
        $todayPaymentTotal = (clone $todayQuery)->sum('amount');

        $providers = PaymentProvider::query()
            ->where('status', PaymentProvider::STATUS_ACTIVE)
            ->orderBy('name')
            ->get(['code', 'name']);

        return view('business.payments.index', compact(
            'payments',
            'providers',
            'filters',
            'todayPaymentCount',
            'todayPaymentTotal'
        ));
    }

    public function show(Request $request, Payment $payment): View
    {
        $this->ensurePaymentBelongsToBusiness($payment);

        $payment->load([
            'provider',
            'emitterDevice',
            'acknowledgements.user',
        ]);

        PaymentAcknowledgement::query()->updateOrCreate(
            [
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'viewed_at' => now(),
            ]
        );

        return view('business.payments.show', compact('payment'));
    }

    public function confirm(
        Request $request,
        Payment $payment
    ): RedirectResponse {
        $this->ensurePaymentBelongsToBusiness($payment);

        DB::transaction(function () use ($request, $payment): void {
            $acknowledgement = PaymentAcknowledgement::query()
                ->firstOrNew([
                    'payment_id' => $payment->id,
                    'user_id' => $request->user()->id,
                ]);

            $acknowledgement->viewed_at ??= now();
            $acknowledgement->confirmed_at = now();
            $acknowledgement->save();

            if ($payment->status === Payment::STATUS_RECEIVED) {
                $payment->update([
                    'status' => Payment::STATUS_CONFIRMED,
                ]);
            }
        });

        return redirect()
            ->route('business.payments.show', $payment)
            ->with('success', 'Pago confirmado como revisado.');
    }

    private function ensurePaymentBelongsToBusiness(
        Payment $payment
    ): void {
        abort_unless(
            $payment->business_id === auth()->user()->business_id,
            404
        );
    }
}