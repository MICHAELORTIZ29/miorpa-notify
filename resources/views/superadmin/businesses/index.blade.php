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
        font-size: 32px;
    }

    .table-container {
        overflow-x: auto;
    }

    .business-table {
        width: 100%;
        border-collapse: collapse;
    }

    .business-table th,
    .business-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .business-table th {
        color: var(--muted);
        font-size: 13px;
        text-transform: uppercase;
    }

    .business-table tr:last-child td {
        border-bottom: 0;
    }

    .status {
        display: inline-block;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-active {
        color: #067647;
        background: #ecfdf3;
    }

    .empty-list {
        padding: 50px 20px;
        color: var(--muted);
        text-align: center;
    }

    @media (max-width: 760px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('superadmin-content')
<div class="page-heading">
    <div>
        <h1>Negocios</h1>
        <p>Administra tus clientes y sus accesos.</p>
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

<section class="panel table-container">
    @if ($businesses->isEmpty())
        <div class="empty-list">
            Todavía no tienes negocios registrados.
        </div>
    @else
        <table class="business-table">
            <thead>
                <tr>
                    <th>Negocio</th>
                    <th>Administrador</th>
                    <th>Usuarios</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($businesses as $business)
                    <tr>
                        <td>
                            <strong>{{ $business->name }}</strong><br>
                            <small>{{ $business->tax_id ?: 'Sin RUC' }}</small>
                        </td>
                        <td>
                            {{ $business->users->first()?->name ?? 'Sin administrador' }}
                        </td>
                        <td>{{ $business->users_count }}</td>
                        <td>
                            <span class="status status-active">
                                {{ ucfirst($business->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('superadmin.businesses.show', $business) }}">
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection