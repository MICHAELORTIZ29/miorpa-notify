<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\PaymentIndexRequest;
use App\Models\Payment;
use App\Models\PaymentAcknowledgement;
use App\Models\PaymentProvider;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Device;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(PaymentIndexRequest $request): View
    {
        $user = $request->user();
        $filters = $request->validated();

        $query = Payment::query()
            ->where('business_id', $user->business_id)
            ->with([
    'provider',
    'emitterDevice',

    'acknowledgements' => fn ($query) =>
        $query
            ->whereNotNull('confirmed_at')
            ->oldest('confirmed_at'),

    'acknowledgements.user:id,name',
    'acknowledgements.receiverDevice:id,name',
])
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
                        ->where(
                            'payer_name',
                            'like',
                            "%{$search}%"
                        )
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
                    fn ($query) => $query->where(
                        'code',
                        $provider
                    )
                )
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $filters['amount'] ?? null,
                fn ($query, $amount) => $query->where(
                    'amount',
                    $amount
                )
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
            ->whereBetween(
                'occurred_at',
                [
                    $todayStart,
                    $todayEnd,
                ]
            );

        $todayPaymentCount = (clone $todayQuery)->count();

        $todayPaymentTotal = (clone $todayQuery)->sum('amount');

        $providers = PaymentProvider::query()
            ->where(
                'status',
                PaymentProvider::STATUS_ACTIVE
            )
            ->orderBy('name')
            ->get([
                'code',
                'name',
            ]);

        $latestPaymentPublicId = Payment::query()
            ->where('business_id', $user->business_id)
            ->latest('occurred_at')
            ->value('public_id');

        return view(
            'business.payments.index',
            compact(
                'payments',
                'providers',
                'filters',
                'todayPaymentCount',
                'todayPaymentTotal',
                'latestPaymentPublicId'
            )
        );
    }

    public function liveStatus(
        Request $request
    ): JsonResponse {
        $latestPayment = Payment::query()
            ->where(
                'business_id',
                $request->user()->business_id
            )
            ->latest('occurred_at')
            ->first([
                'public_id',
                'occurred_at',
            ]);

        return response()->json([
            'latest_payment_public_id' =>
                $latestPayment?->public_id,

            'latest_payment_at' =>
                $latestPayment?->occurred_at?->toISOString(),

            'checked_at' => now()->toISOString(),
        ]);
    }
    public function export(
    PaymentIndexRequest $request
): StreamedResponse {
    $user = $request->user();

    abort_unless(
        $user->isAdministrator(),
        403
    );

    $filters = $request->validated();

    $timezone = $user->business?->timezone
        ?: 'America/Lima';

    $query = Payment::query()
        ->where(
            'business_id',
            $user->business_id
        )
        ->with([
            'provider:id,name',
            'emitterDevice:id,name',

            'acknowledgements' => fn ($query) =>
                $query
                    ->whereNotNull('confirmed_at')
                    ->oldest('confirmed_at'),

            'acknowledgements.user:id,name',
            'acknowledgements.receiverDevice:id,name',
        ])
        ->when(
            $filters['search'] ?? null,
            fn ($query, $search) =>
                $query->where(
                    fn ($query) =>
                        $query
                            ->where(
                                'payer_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'external_reference',
                                'like',
                                "%{$search}%"
                            )
                )
        )
        ->when(
            $filters['provider'] ?? null,
            fn ($query, $provider) =>
                $query->whereHas(
                    'provider',
                    fn ($query) =>
                        $query->where(
                            'code',
                            $provider
                        )
                )
        )
        ->when(
            $filters['status'] ?? null,
            fn ($query, $status) =>
                $query->where(
                    'status',
                    $status
                )
        )
        ->when(
            $filters['amount'] ?? null,
            fn ($query, $amount) =>
                $query->where(
                    'amount',
                    $amount
                )
        )
        ->when(
            $filters['date_from'] ?? null,
            fn ($query, $date) =>
                $query->where(
                    'occurred_at',
                    '>=',
                    CarbonImmutable::parse(
                        $date,
                        $timezone
                    )
                        ->startOfDay()
                        ->utc()
                )
        )
        ->when(
            $filters['date_to'] ?? null,
            fn ($query, $date) =>
                $query->where(
                    'occurred_at',
                    '<=',
                    CarbonImmutable::parse(
                        $date,
                        $timezone
                    )
                        ->endOfDay()
                        ->utc()
                )
        )
        ->orderBy('id');

    $fileName = sprintf(
        'pagos-%s-%s.csv',
        $user->business?->public_id
            ?? 'negocio',
        now($timezone)->format('Y-m-d-His')
    );

    return response()->streamDownload(
        function () use (
            $query,
            $timezone
        ): void {
            $output = fopen(
                'php://output',
                'wb'
            );

            /*
             * BOM para que Excel reconozca correctamente
             * tildes, eñes y caracteres UTF-8.
             */
            fwrite(
                $output,
                "\xEF\xBB\xBF"
            );

            fputcsv(
                $output,
                [
                    'Fecha',
                    'Hora',
                    'Cliente',
                    'Medio',
                    'Monto',
                    'Moneda',
                    'Estado',
                    'Verificado por',
                    'Dispositivo receptor',
                    'Fecha de verificación',
                    'Dispositivo emisor',
                    'Referencia',
                ],
                ';'
            );

            $query->chunkById(
                500,
                function ($payments) use (
                    $output,
                    $timezone
                ): void {
                    foreach (
                        $payments as $payment
                    ) {
                        $confirmation =
                            $payment
                                ->acknowledgements
                                ->first();

                        $status = match (
                            $payment->status
                        ) {
                            Payment::STATUS_CONFIRMED =>
                                'Verificado',

                            Payment::STATUS_IGNORED =>
                                'Ignorado',

                            default =>
                                'Recibido',
                        };

                        fputcsv(
                            $output,
                            [
                                $payment
                                    ->occurred_at
                                    ->timezone(
                                        $timezone
                                    )
                                    ->format(
                                        'd/m/Y'
                                    ),

                                $payment
                                    ->occurred_at
                                    ->timezone(
                                        $timezone
                                    )
                                    ->format(
                                        'H:i:s'
                                    ),

                                $this
                                    ->safeCsvValue(
                                        $payment
                                            ->payer_name
                                        ?: 'No identificado'
                                    ),

                                $payment
                                    ->provider
                                    ?->name
                                    ?? 'No identificado',

                                number_format(
                                    (float)
                                        $payment
                                            ->amount,
                                    2,
                                    '.',
                                    ''
                                ),

                                $payment->currency,

                                $status,

                                $this
                                    ->safeCsvValue(
                                        $confirmation
                                            ?->user
                                            ?->name
                                        ?? ''
                                    ),

                                $this
                                    ->safeCsvValue(
                                        $confirmation
                                            ?->receiverDevice
                                            ?->name
                                        ?? ''
                                    ),

                                $confirmation
                                    ?->confirmed_at
                                    ?->timezone(
                                        $timezone
                                    )
                                    ->format(
                                        'd/m/Y H:i:s'
                                    )
                                    ?? '',

                                $this
                                    ->safeCsvValue(
                                        $payment
                                            ->emitterDevice
                                            ?->name
                                        ?? ''
                                    ),

                                $this
                                    ->safeCsvValue(
                                        $payment
                                            ->external_reference
                                        ?? ''
                                    ),
                            ],
                            ';'
                        );
                    }
                }
            );

            fclose($output);
        },
        $fileName,
        [
            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Cache-Control' =>
                'no-store, no-cache',
        ]
    );
}

   public function show(
    Request $request,
    Payment $payment
): View {
    $this->ensurePaymentBelongsToBusiness($payment);

    $receiverDevice = $request->attributes->get(
        'receiver_device'
    );

    abort_unless(
        $receiverDevice instanceof Device,
        403,
        'El dispositivo receptor no está vinculado.'
    );

    /*
     * Registramos la primera vez que este usuario
     * visualizó el pago.
     *
     * Si ya confirmó anteriormente, no cambiamos el
     * dispositivo desde el cual confirmó.
     */
    $acknowledgement =
        PaymentAcknowledgement::query()
            ->firstOrNew([
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
            ]);

    $acknowledgement->viewed_at ??= now();

    if ($acknowledgement->confirmed_at === null) {
        $acknowledgement->receiver_device_id =
            $receiverDevice->id;
    }

    $acknowledgement->save();

    /*
     * Cargamos las relaciones después de registrar
     * la visualización para que aparezca inmediatamente.
     */
    $payment->load([
        'provider',
        'emitterDevice',
        'acknowledgements' => fn ($query) =>
            $query->orderByDesc('confirmed_at')
                ->orderByDesc('viewed_at'),
        'acknowledgements.user',
        'acknowledgements.receiverDevice',
    ]);

    return view(
        'business.payments.show',
        compact('payment')
    );
}

public function confirm(
    Request $request,
    Payment $payment
): RedirectResponse {
    $this->ensurePaymentBelongsToBusiness($payment);

    $receiverDevice = $request->attributes->get(
        'receiver_device'
    );

    abort_unless(
        $receiverDevice instanceof Device,
        403,
        'El dispositivo receptor no está vinculado.'
    );

    $result = DB::transaction(
        function () use (
            $request,
            $payment,
            $receiverDevice
        ): array {
            $lockedPayment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Buscamos si otro trabajador ya confirmó
             * este pago.
             */
            $existingConfirmation =
                PaymentAcknowledgement::query()
                    ->where(
                        'payment_id',
                        $lockedPayment->id
                    )
                    ->whereNotNull('confirmed_at')
                    ->with('user:id,name')
                    ->oldest('confirmed_at')
                    ->first();

            if ($existingConfirmation) {
                return [
                    'confirmed' => false,
                    'user_name' =>
                        $existingConfirmation
                            ->user
                            ?->name
                        ?? 'otro usuario',
                ];
            }

            $acknowledgement =
                PaymentAcknowledgement::query()
                    ->firstOrNew([
                        'payment_id' =>
                            $lockedPayment->id,

                        'user_id' =>
                            $request->user()->id,
                    ]);

            $acknowledgement->viewed_at ??= now();

            $acknowledgement->confirmed_at = now();

            $acknowledgement->receiver_device_id =
                $receiverDevice->id;

            $acknowledgement->save();

            $lockedPayment->update([
                'status' =>
                    Payment::STATUS_CONFIRMED,
            ]);

            return [
                'confirmed' => true,
                'user_name' =>
                    $request->user()->name,
            ];
        }
    );

    if (! $result['confirmed']) {
        return redirect()
            ->route(
                'business.payments.show',
                $payment
            )
            ->with(
                'warning',
                "Este pago ya fue verificado por {$result['user_name']}."
            );
    }

    return redirect()
        ->route(
            'business.payments.show',
            $payment
        )
        ->with(
            'success',
            'Pago marcado como verificado correctamente.'
        );
}
private function safeCsvValue(
    ?string $value
): string {
    $value = trim(
        (string) $value
    );

    /*
     * Evita que Excel interprete datos proporcionados
     * externamente como fórmulas.
     */
    if (
        $value !== '' &&
        in_array(
            $value[0],
            ['=', '+', '-', '@'],
            true
        )
    ) {
        return "'".$value;
    }

    return $value;
}
 

    private function ensurePaymentBelongsToBusiness(
        Payment $payment
    ): void {
        abort_unless(
            $payment->business_id ===
                auth()->user()->business_id,
            404
        );
    }
    
}