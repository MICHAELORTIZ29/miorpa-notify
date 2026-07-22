@php
    $subscription = $business?->currentSubscription;

    $graceDays = $subscription?->grace_ends_at
        ? $subscription->current_period_ends_at
            ->diffInDays($subscription->grace_ends_at)
        : 3;
@endphp

<section class="subscription-section">
    <h2>Plan, facturación y restricciones</h2>

    <p>
        Estas condiciones se aplicarán a todo el negocio.
    </p>

    <div class="subscription-grid">
        <div class="subscription-field">
            <label for="plan_id">
                Plan *
            </label>

            <select
                id="plan_id"
                name="plan_id"
                required
            >
                <option value="">
                    Selecciona un plan
                </option>

                @foreach ($plans as $plan)
                    <option
                        value="{{ $plan->id }}"
                        @selected(
                            (string) old(
                                'plan_id',
                                $subscription?->plan_id
                            ) === (string) $plan->id
                        )
                    >
                        {{ $plan->name }}
                    </option>
                @endforeach
            </select>

            @error('plan_id')
                <span class="field-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="subscription-field">
            <label for="billing_cycle">
                Ciclo de pago *
            </label>

            <select
                id="billing_cycle"
                name="billing_cycle"
                required
            >
                <option
                    value="monthly"
                    @selected(
                        old(
                            'billing_cycle',
                            $subscription?->billing_cycle
                                ?? 'monthly'
                        ) === 'monthly'
                    )
                >
                    Mensual
                </option>

                <option
                    value="annual"
                    @selected(
                        old(
                            'billing_cycle',
                            $subscription?->billing_cycle
                        ) === 'annual'
                    )
                >
                    Anual
                </option>
            </select>
        </div>

        <div class="subscription-field">
            <label for="subscription_price">
                Precio (S/)
            </label>

            <input
                id="subscription_price"
                name="subscription_price"
                type="number"
                min="0"
                step="0.01"
                value="{{ old(
                    'subscription_price',
                    $subscription?->price
                ) }}"
            >

            @error('subscription_price')
                <span class="field-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="subscription-field">
            <label for="grace_days">
                Días de gracia *
            </label>

            <input
                id="grace_days"
                name="grace_days"
                type="number"
                min="0"
                max="60"
                value="{{ old(
                    'grace_days',
                    $graceDays
                ) }}"
                required
            >

            @error('grace_days')
                <span class="field-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="subscription-field">
            <label for="subscription_starts_at">
                Fecha de inicio *
            </label>

            <input
                id="subscription_starts_at"
                name="subscription_starts_at"
                type="date"
                value="{{ old(
                    'subscription_starts_at',
                    $subscription?->starts_at
                        ?->timezone('America/Lima')
                        ->format('Y-m-d')
                        ?? now('America/Lima')
                            ->format('Y-m-d')
                ) }}"
                required
            >

            @error('subscription_starts_at')
                <span class="field-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="subscription-field">
            <label for="subscription_ends_at">
                Próxima fecha de pago *
            </label>

            <input
                id="subscription_ends_at"
                name="subscription_ends_at"
                type="date"
                value="{{ old(
                    'subscription_ends_at',
                    $subscription?->current_period_ends_at
                        ?->timezone('America/Lima')
                        ->format('Y-m-d')
                        ?? now('America/Lima')
                            ->addMonth()
                            ->format('Y-m-d')
                ) }}"
                required
            >

            @error('subscription_ends_at')
                <span class="field-error">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="subscription-field">
            <label for="max_emitters">
                Máximo de emisores *
            </label>

            <input
                id="max_emitters"
                name="max_emitters"
                type="number"
                min="0"
                value="{{ old(
                    'max_emitters',
                    $subscription?->limit(
                        \App\Models\Subscription::LIMIT_EMITTERS
                    ) ?? 1
                ) }}"
                required
            >
        </div>

        <div class="subscription-field">
            <label for="max_receivers">
                Máximo de receptores *
            </label>

            <input
                id="max_receivers"
                name="max_receivers"
                type="number"
                min="0"
                value="{{ old(
                    'max_receivers',
                    $subscription?->limit(
                        \App\Models\Subscription::LIMIT_RECEIVERS
                    ) ?? 3
                ) }}"
                required
            >
        </div>

        <div class="subscription-field">
            <label for="max_cashiers">
                Máximo de cajeros *
            </label>

            <input
                id="max_cashiers"
                name="max_cashiers"
                type="number"
                min="0"
                value="{{ old(
                    'max_cashiers',
                    $subscription?->limit(
                        \App\Models\Subscription::LIMIT_CASHIERS
                    ) ?? 3
                ) }}"
                required
            >
        </div>

        <label class="subscription-check">
            <input
                type="hidden"
                name="auto_suspend"
                value="0"
            >

            <input
                type="checkbox"
                name="auto_suspend"
                value="1"
                @checked(
                    old(
                        'auto_suspend',
                        $subscription?->auto_suspend ?? true
                    )
                )
            >

            Suspender automáticamente al terminar la gracia
        </label>
    </div>
</section>

@push('styles')
    <style>
        .subscription-section {
            padding-top: 28px;
            margin-top: 30px;
            border-top: 1px solid var(--border);
        }

        .subscription-section > p {
            color: var(--muted);
        }

        .subscription-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .subscription-field {
            display: grid;
            gap: 7px;
        }

        .subscription-field label,
        .subscription-check {
            font-weight: 700;
        }

        .subscription-field input,
        .subscription-field select {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: white;
            font: inherit;
        }

        .subscription-check {
            display: flex;
            align-items: center;
            grid-column: 1 / -1;
            gap: 10px;
        }

        .field-error {
            color: #b42318;
            font-size: 14px;
        }

        @media (max-width: 700px) {
            .subscription-grid {
                grid-template-columns: 1fr;
            }

            .subscription-check {
                grid-column: auto;
            }
        }
    </style>
@endpush