@extends('layouts.app')

@push('styles')
<style>
    .admin-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 16px 4vw;
        color: white;
        background: var(--primary-dark);
    }

    .admin-nav {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-nav a {
        padding: 10px 14px;
        color: #d8e7f4;
        text-decoration: none;
        border-radius: 9px;
    }

    .admin-nav a:hover,
    .admin-nav a.active {
        color: white;
        background: rgba(255, 255, 255, .12);
    }

    .admin-user {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .admin-user-data {
        text-align: right;
    }

    .admin-user-data small {
        display: block;
        margin-top: 3px;
        color: #bdd0e2;
    }

    .admin-main {
        width: min(1240px, 92%);
        margin: 34px auto;
    }

    .page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .page-heading h1 {
        margin: 0 0 7px;
    }

    .page-heading p {
        margin: 0;
        color: var(--muted);
    }

    .panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(18, 58, 99, .05);
    }

    .button-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    @media (max-width: 850px) {
        .admin-nav {
            display: none;
        }

        .admin-user-data {
            display: none;
        }

        .page-heading {
            align-items: stretch;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<header class="admin-header">
    <div class="brand">
        <span class="brand-mark">MN</span>
        <span>MIORPA NOTIFY</span>
    </div>

    <nav class="admin-nav">
        <a
            href="{{ route('superadmin.businesses.index') }}"
            class="{{ request()->routeIs('superadmin.businesses.*') ? 'active' : '' }}"
        >
            Negocios
        </a>
    </nav>

    <div class="admin-user">
        <div class="admin-user-data">
            <strong>{{ auth()->user()->name }}</strong>
            <small>Superadministrador</small>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="button button-secondary" type="submit">
                Salir
            </button>
        </form>
    </div>
</header>

<main class="admin-main">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('superadmin-content')
</main>
@endsection