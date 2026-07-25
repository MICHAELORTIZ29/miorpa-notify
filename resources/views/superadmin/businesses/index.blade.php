@extends('superadmin.layout')

@section('title', 'Negocios | MIORPA NOTIFY')

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .summary-card {
        padding: 22px;
    }

    .summary-card small {
        color: var(--muted);
    }

    .summary-card strong {
        display: block;
        margin-top: 10px;
        color: var(--primary-dark);
        font-size: 32px;
    }

    .business-filters {
        display: grid;
        grid-template-columns: 1fr 220px auto;
        gap: 12px;
        margin-bottom: 20px;
        padding: 16px;
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
    }

    .business-filters input,
    .business-filters select {
        width: 100%;
        min-height: 44px;
        box-sizing: border-box;
        padding: 0 13px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        font: inherit;
    }

    .business-filters input:focus,
    .business-filters select:focus {
        border-color: #1464d2;
        outline: none;
        box-shadow: 0 0 0 3px rgba(20, 100, 210, .12);
    }

    .table-container {
        overflow-x: auto;
    }

    .business-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .business-table th,
    .business-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .business-table th {
        color: var(--muted);
        font-size: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .business-table tr:last-child td {
        border-bottom: 0;
    }

    .business-name {
        color: var(--primary-dark);
        font-weight: 800;
    }

    .business-secondary {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 13px;
    }

    .plan-name {
        color: var(--primary-dark);
        font-weight: 700;
    }

    .plan-cycle {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
    }

    .expiry-date {
        color: var(--primary-dark);
        font-weight: 700;
        white-space: nowrap;
    }

    .expiry-warning {
        display: block;
        margin-top: 4px;
        color: #b54708;
        font-size: 12px;
        font-weight: 700;
    }

    .expiry-danger {
        color: #b42318;
    }

    .expiry-ok {
        display: block;
        margin-top: 4px;
        color: #067647;
        font-size: 12px;
    }

    .status {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-active {
        color: #067647;
        background: #ecfdf3;
    }

    .status-trial {
        color: #175cd3;
        background: #eff8ff;
    }

    .status-overdue {
        color: #b54708;
        background: #fff4e5;
    }

    .status-suspended,
    .status-closed {
        color: #b42318;
        background: #fef3f2;
    }

    .empty-list {
        padding: 50px 20px;
        color: var(--muted);
        text-align: center;
    }

    .pagination-wrapper {
        padding: 18px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 850px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .business-filters {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('superadmin-content')
<div class="page-heading">
    <div>
        <h1>Negocios</h1>
        <p>Administra tus clientes, planes y accesos.</p>
    </div>

    <a
        class="button button-primary button-link"
        href="{{ route('superadmin.businesses.create') }}"
    >
        Crear negocio
    </a>
</div>

<section class="summary-grid">
    <article class="panel summary-card">
        <small>Total de negocios</small>
        <strong>{{ $businesses->total() }}</strong>
    </article>

    <article class="panel summary-card">
        <small>Activos</small>
        <strong>{{ $activeBusinesses }}</strong>
    </article>

    <article class="panel summary-card">
        <small>En prueba</small>
        <strong>{{ $trialBusinesses }}</strong>
    </article>
</section>

<form
    method="GET"
    action="{{ route('superadmin.businesses.index') }}"
    class="business-filters"
>
    <input
        type="search"
        name="search"
        value="{{ $search }}"
        placeholder="Buscar negocio, RUC o administrador..."
    >

    <select name="status">
        <option value="">Todos los estados</option>
        <option
            value="active"
            @selected($status === 'active')
        >
            Activos
        </option>
        <option
            value="trial"
            @selected($status === 'trial')
        >
            En prueba
        </option>
        <option
            value="overdue"
            @selected($status === 'overdue')
        >
            Vencidos
        </option>
        <option
            value="suspended"
            @selected($status === 'suspended')
        >
            Suspendidos
        </option>
        <option
            value="closed"
            @selected($status === 'closed')
        >
            Cerrados
        </option>
    </select>

    <button
        type="submit"
        class="button button-primary"
    >
        Buscar
    </button>
</form>

<section class="panel table-container">
    @if ($businesses->isEmpty())
        <div class="empty-list">
            No se encontraron negocios con esos criterios.
        </div>
    @else
        <table class="business-table">
            <thead>
                <tr>
                    <th>Negocio</th>
                    <th>Administrador</th>
                    <th>Plan</th>
                    <th>Vencimiento</th>
                    <th>Usuarios</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($businesses as $business)
                    @php
                        $subscription =
                            $business->currentSubscription;

                        $endDate =
                            $subscription?->current_period_ends_at;

                        $daysRemaining = $endDate
                            ? now()
                                ->startOfDay()
                                ->diffInDays(
                                    $endDate->copy()->startOfDay(),
                                    false
                                )
                            : null;

                        $statusClass =
                            'status-' . $business->status;
                    @endphp

                    <tr>
                        <td>
                            <span class="business-name">
                                {{ $business->name }}
                            </span>

                            <span class="business-secondary">
                                {{ $business->tax_id ?: 'Sin RUC' }}
                            </span>
                        </td>

                        <td>
                            {{ $business->users->first()?->name
                                ?? 'Sin administrador' }}
                        </td>

                        <td>
                            @if ($subscription?->plan)
                                <span class="plan-name">
                                    {{ $subscription->plan->name }}
                                </span>

                                <span class="plan-cycle">
                                    {{ $subscription->billing_cycle === 'annual'
                                        ? 'Anual'
                                        : 'Mensual' }}
                                </span>
                            @else
                                <span class="business-secondary">
                                    Sin plan
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($endDate)
                                <span class="expiry-date">
                                    {{ $endDate->format('d/m/Y') }}
                                </span>

                                @if ($daysRemaining < 0)
                                    <span class="expiry-warning expiry-danger">
                                        Vencido hace
                                        {{ abs($daysRemaining) }} días
                                    </span>
                                @elseif ($daysRemaining <= 7)
                                    <span class="expiry-warning">
                                        Vence en
                                        {{ $daysRemaining }} días
                                    </span>
                                @else
                                    <span class="expiry-ok">
                                        Vigente
                                    </span>
                                @endif
                            @else
                                <span class="business-secondary">
                                    Sin fecha
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $business->users_count }}
                        </td>

                        <td>
                            <span class="status {{ $statusClass }}">
                                {{ ucfirst($business->status) }}
                            </span>
                        </td>

                        <td>
                            <a
                                href="{{ route(
                                    'superadmin.businesses.show',
                                    $business
                                ) }}"
                            >
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $businesses->links() }}
        </div>
    @endif
</section>
@endsection