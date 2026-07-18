@extends('superadmin.layout')

@section('title', $business->name.' | MIORPA NOTIFY')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .detail-card {
        padding: 24px;
    }

    .detail-card h2 {
        margin-top: 0;
    }

    .detail-list {
        display: grid;
        gap: 15px;
    }

    .detail-item small {
        display: block;
        margin-bottom: 4px;
        color: var(--muted);
    }

    .user-row {
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
    }

    .user-row:last-child {
        border-bottom: 0;
    }

    @media (max-width: 760px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('superadmin-content')
<div class="page-heading">
    <div>
        <h1>{{ $business->name }}</h1>
        <p>Detalle general del negocio.</p>
    </div>

    <a
        class="button button-secondary button-link"
        href="{{ route('superadmin.businesses.index') }}"
    >
        Volver
    </a>
</div>

<section class="detail-grid">
    <article class="panel detail-card">
        <h2>Información</h2>

        <div class="detail-list">
            <div class="detail-item">
                <small>Nombre comercial</small>
                <strong>{{ $business->name }}</strong>
            </div>

            <div class="detail-item">
                <small>Razón social</small>
                <strong>{{ $business->legal_name ?: 'No registrada' }}</strong>
            </div>

            <div class="detail-item">
                <small>RUC</small>
                <strong>{{ $business->tax_id ?: 'No registrado' }}</strong>
            </div>

            <div class="detail-item">
                <small>Estado</small>
                <strong>{{ ucfirst($business->status) }}</strong>
            </div>
        </div>
    </article>

    <article class="panel detail-card">
        <h2>Usuarios</h2>

        @forelse ($business->users as $user)
            <div class="user-row">
                <strong>{{ $user->name }}</strong><br>
                <small>{{ $user->email }} · {{ $user->role_code }}</small>
            </div>
        @empty
            <p>No hay usuarios registrados.</p>
        @endforelse
    </article>
</section>
@endsection