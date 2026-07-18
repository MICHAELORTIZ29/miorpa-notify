@extends('layouts.app')

@section('title', 'Dashboard | MIORPA NOTIFY')

@push('styles')
<style>
    .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 18px 5vw;
        color: white;
        background: var(--primary-dark);
    }

    .user-area {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .user-data {
        text-align: right;
    }

    .user-data strong,
    .user-data small {
        display: block;
    }

    .user-data small {
        margin-top: 3px;
        color: #bdd0e2;
    }

    .dashboard {
        width: min(1180px, 92%);
        margin: 38px auto;
    }

    .dashboard h1 {
        margin-bottom: 8px;
    }

    .dashboard-subtitle {
        margin-top: 0;
        color: var(--muted);
    }

    .cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 32px;
    }

    .card {
        padding: 24px;
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(18, 58, 99, .06);
    }

    .card small {
        color: var(--muted);
    }

    .card strong {
        display: block;
        margin-top: 12px;
        font-size: 32px;
    }

    .empty {
        margin-top: 24px;
        padding: 36px;
        color: var(--muted);
        text-align: center;
        background: white;
        border: 1px dashed var(--border);
        border-radius: 16px;
    }

    @media (max-width: 760px) {
        .cards {
            grid-template-columns: 1fr;
        }

        .user-data {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<header class="topbar">
    <div class="brand">
        <span class="brand-mark">MN</span>
        <span>MIORPA NOTIFY</span>
    </div>

    <div class="user-area">
        <div class="user-data">
            <strong>{{ $user->name }}</strong>
            <small>{{ $user->role_code }}</small>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="button button-secondary" type="submit">
                Cerrar sesión
            </button>
        </form>
    </div>
</header>

<main class="dashboard">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1>Resumen</h1>

    <p class="dashboard-subtitle">
        {{ $user->business?->name ?? 'Administración general de la plataforma' }}
    </p>

    <section class="cards">
        <article class="card">
            <small>Pagos recibidos hoy</small>
            <strong>0</strong>
        </article>

        <article class="card">
            <small>Total recibido hoy</small>
            <strong>S/ 0.00</strong>
        </article>

        <article class="card">
            <small>Dispositivos conectados</small>
            <strong>0</strong>
        </article>
    </section>

    <section class="empty">
        El sistema está listo. Los módulos operativos se agregarán en los siguientes pasos.
    </section>
</main>
@endsection