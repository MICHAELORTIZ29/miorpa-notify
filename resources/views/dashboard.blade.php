@extends('business.layout')

@section('title', 'Inicio | MIORPA NOTIFY')

@push('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .dashboard-card {
        padding: 22px;
    }

    .dashboard-card small {
        display: block;
        color: var(--muted);
    }

    .dashboard-card strong {
        display: block;
        margin-top: 9px;
        color: var(--primary-dark);
        font-size: 31px;
    }

    .dashboard-sections {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
    }

    .dashboard-panel {
        padding: 23px;
    }

    .dashboard-panel h2 {
        margin: 0 0 18px;
    }

    .payment-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }

    .payment-row:last-child {
        border-bottom: 0;
    }

    .payment-client small {
        display: block;
        margin-top: 4px;
        color: var(--muted);
    }

    .payment-amount {
        font-size: 18px;
        font-weight: 800;
        white-space: nowrap;
    }

    .usage-list {
        display: grid;
        gap: 17px;
    }

    .usage-item {
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border);
    }

    .usage-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .usage-heading {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 8px;
    }

    .usage-heading span {
        color: var(--muted);
    }

    .usage-bar {
        height: 9px;
        overflow: hidden;
        background: #e8eef4;
        border-radius: 999px;
    }

    .usage-bar div {
        height: 100%;
        background: #079b98;
        border-radius: inherit;
    }

    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .empty-dashboard {
        padding: 30px;
        color: var(--muted);
        text-align: center;
    }

    @media (max-width: 1050px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .dashboard-sections {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 620px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .payment-row {
            grid-template-columns: 1fr auto;
        }

        .payment-row .payment-action {
            grid-column: 1 / -1;
        }
    }
</style>
@endpush

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Resumen</h1>

        <p>
            Estado actual de {{ $business->name }}.
        </p>
    </div>

    <div class="quick-actions">
        <a
            class="button button-secondary button-link"
            href="{{ route('business.users.create') }}"
        >
            Crear cajero
        </a>

        <a
            class="button button-secondary button-link"
            href="{{ route('business.devices.index') }}"
        >
            Vincular dispositivo
        </a>

        <a
            class="button button-link"
            href="{{ route('business.payments.index') }}"
        >
            Ver pagos
        </a>
    </div>
</div>

<section class="dashboard-grid">
    <article class="panel dashboard-card">
        <small>Pagos recibidos hoy</small>

        <strong>{{ $todayPaymentCount }}</strong>
    </article>

    <article class="panel dashboard-card">
        <small>Total recibido hoy</small>

        <strong>
            S/ {{ number_format($todayPaymentTotal, 2) }}
        </strong>
    </article>

    <article class="panel dashboard-card">
        <small>Dispositivos conectados</small>

        <strong>{{ $connectedDevices }}</strong>
    </article>

    <article class="panel dashboard-card">
        <small>Cajeros activos</small>

        <strong>{{ $activeCashiers }}</strong>
    </article>
</section>

<section class="dashboard-sections">
    <article class="panel dashboard-panel">
        <h2>Últimos pagos</h2>

        @forelse ($latestPayments as $payment)
            <div class="payment-row">
                <div class="payment-client">
                    <strong>
                        {{ $payment->payer_name
                            ?: 'Cliente no identificado' }}
                    </strong>

                    <small>
                        {{ $payment->provider?->name
                            ?? 'Medio no identificado' }}

                        ·

                        {{ $payment->occurred_at
                            ->timezone($business->timezone)
                            ->format('d/m/Y H:i:s') }}
                    </small>
                </div>

                <div class="payment-amount">
                    S/ {{ number_format($payment->amount, 2) }}
                </div>

                <a
                    class="button button-secondary button-link payment-action"
                    href="{{ route(
                        'business.payments.show',
                        $payment
                    ) }}"
                >
                    Ver
                </a>
            </div>
        @empty
            <div class="empty-dashboard">
                Todavía no se registraron pagos.
            </div>
        @endforelse
    </article>

    <article class="panel dashboard-panel">
        <h2>Uso del plan</h2>

        @php
            $emitterMaximum = max(
                1,
                (int) ($emitterLimit ?? 0)
            );

            $receiverMaximum = max(
                1,
                (int) ($receiverLimit ?? 0)
            );

            $cashierMaximum = max(
                1,
                (int) ($cashierLimit ?? 0)
            );
        @endphp

        <div class="usage-list">
            <div class="usage-item">
                <div class="usage-heading">
                    <span>Emisores</span>

                    <strong>
                        {{ $activeEmitters }} /
                        {{ $emitterLimit ?? 'Sin límite' }}
                    </strong>
                </div>

                <div class="usage-bar">
                    <div
                        style="width: {{ min(
                            100,
                            ($activeEmitters / $emitterMaximum) * 100
                        ) }}%"
                    ></div>
                </div>
            </div>

            <div class="usage-item">
                <div class="usage-heading">
                    <span>Receptores</span>

                    <strong>
                        {{ $activeReceivers }} /
                        {{ $receiverLimit ?? 'Sin límite' }}
                    </strong>
                </div>

                <div class="usage-bar">
                    <div
                        style="width: {{ min(
                            100,
                            ($activeReceivers / $receiverMaximum) * 100
                        ) }}%"
                    ></div>
                </div>
            </div>

            <div class="usage-item">
                <div class="usage-heading">
                    <span>Cajeros</span>

                    <strong>
                        {{ $activeCashiers }} /
                        {{ $cashierLimit ?? 'Sin límite' }}
                    </strong>
                </div>

                <div class="usage-bar">
                    <div
                        style="width: {{ min(
                            100,
                            ($activeCashiers / $cashierMaximum) * 100
                        ) }}%"
                    ></div>
                </div>
            </div>
        </div>

        @if ($subscription)
            <div
                style="
                    margin-top: 22px;
                    color: var(--muted);
                    line-height: 1.6;
                "
            >
                <div>
                    Plan:
                    <strong>{{ $subscription->plan->name }}</strong>
                </div>

                <div>
                    Estado:
                    <strong>{{ ucfirst($subscription->status) }}</strong>
                </div>

                <div>
                    Próximo vencimiento:
                    <strong>
                        {{ $subscription->current_period_ends_at
                            ->timezone($business->timezone)
                            ->format('d/m/Y') }}
                    </strong>
                </div>
            </div>
        @else
            <div class="alert alert-warning" style="margin-top: 20px;">
                El negocio no tiene una suscripción asignada.
            </div>
        @endif
    </article>
</section>
@endsection