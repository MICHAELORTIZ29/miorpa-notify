@extends('business.layout')

@section('title', 'Pagos | MIORPA NOTIFY')

@push('styles')
<style>
    .payment-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .summary-card {
        padding: 22px;
    }

    .summary-card span {
        color: var(--muted);
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        font-size: 34px;
    }

    .filters {
        display: grid;
        grid-template-columns: 2fr repeat(4, 1fr) auto;
        gap: 12px;
        padding: 20px;
        margin-bottom: 22px;
    }

    .filters input,
    .filters select {
        min-width: 0;
        padding: 11px;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: white;
        font: inherit;
    }

    .payments-table {
        width: 100%;
        border-collapse: collapse;
    }

    .payments-table th,
    .payments-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .amount {
        font-size: 18px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-received {
        color: #92400e;
        background: #fff4df;
    }

    .status-confirmed {
        color: #08783e;
        background: #e9f9ef;
    }

    .status-ignored {
        color: #b42318;
        background: #feeceb;
    }

    .empty-state {
        padding: 45px;
        text-align: center;
        color: var(--muted);
    }

    @media (max-width: 1000px) {
        .filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 680px) {
        .payment-summary,
        .filters {
            grid-template-columns: 1fr;
        }

        .table-scroll {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Pagos</h1>
        <p>Consulta y confirma los pagos recibidos.</p>
    </div>
</div>

<section class="payment-summary">
    <article class="panel summary-card">
        <span>Pagos recibidos hoy</span>
        <strong>{{ $todayPaymentCount }}</strong>
    </article>

    <article class="panel summary-card">
        <span>Total recibido hoy</span>
        <strong>S/ {{ number_format($todayPaymentTotal, 2) }}</strong>
    </article>
</section>

<form
    class="panel filters"
    method="GET"
    action="{{ route('business.payments.index') }}"
>
    <input
        name="search"
        type="search"
        placeholder="Buscar cliente o referencia"
        value="{{ $filters['search'] ?? '' }}"
    >

    <select name="provider">
        <option value="">Todos los medios</option>

        @foreach ($providers as $provider)
            <option
                value="{{ $provider->code }}"
                @selected(
                    ($filters['provider'] ?? '') === $provider->code
                )
            >
                {{ $provider->name }}
            </option>
        @endforeach
    </select>

    <select name="status">
        <option value="">Todos los estados</option>
        <option
            value="received"
            @selected(($filters['status'] ?? '') === 'received')
        >
            Pendiente
        </option>
        <option
            value="confirmed"
            @selected(($filters['status'] ?? '') === 'confirmed')
        >
            Confirmado
        </option>
    </select>

    <input
        name="date_from"
        type="date"
        value="{{ $filters['date_from'] ?? '' }}"
        title="Desde"
    >

    <input
        name="date_to"
        type="date"
        value="{{ $filters['date_to'] ?? '' }}"
        title="Hasta"
    >

    <button class="button" type="submit">
        Buscar
    </button>
</form>

<section class="panel table-scroll">
    @if ($payments->isEmpty())
        <div class="empty-state">
            No se encontraron pagos.
        </div>
    @else
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Medio</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>
                            {{ $payment->occurred_at
                                ->timezone('America/Lima')
                                ->format('d/m/Y H:i:s') }}
                        </td>

                        <td>
                            <strong>
                                {{ $payment->payer_name ?: 'No identificado' }}
                            </strong>

                            @if ($payment->external_reference)
                                <br>
                                <small>
                                    {{ $payment->external_reference }}
                                </small>
                            @endif
                        </td>

                        <td>{{ $payment->provider->name }}</td>

                        <td class="amount">
                            S/ {{ number_format($payment->amount, 2) }}
                        </td>

                        <td>
                            <span class="status status-{{ $payment->status }}">
                                {{ $payment->status === 'confirmed'
                                    ? 'Confirmado'
                                    : 'Pendiente' }}
                            </span>
                        </td>

                        <td>
                            <a
                                class="button button-secondary button-link"
                                href="{{ route(
                                    'business.payments.show',
                                    $payment
                                ) }}"
                            >
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $payments->links() }}
    @endif
</section>
@endsection