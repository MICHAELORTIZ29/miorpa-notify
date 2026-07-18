@extends('layouts.app')

@push('styles')
<style>
    .business-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 16px 4vw;
        color: white;
        background: var(--primary-dark);
    }

    .business-nav {
        display: flex;
        gap: 10px;
    }

    .business-nav a {
        padding: 10px 14px;
        color: #d8e7f4;
        text-decoration: none;
        border-radius: 9px;
    }

    .business-nav a:hover,
    .business-nav a.active {
        color: white;
        background: rgba(255, 255, 255, .12);
    }

    .business-user {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .business-user-data {
        text-align: right;
    }

    .business-user-data small {
        display: block;
        color: #bdd0e2;
    }

    .business-main {
        width: min(1240px, 92%);
        margin: 34px auto;
    }

    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }

    .page-heading h1 {
        margin: 0 0 6px;
    }

    .page-heading p {
        margin: 0;
        color: var(--muted);
    }

    .panel {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
    }

    .button-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    @media (max-width: 760px) {
        .business-nav,
        .business-user-data {
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
<header class="business-header">
    <div class="brand">
        <span class="brand-mark">MN</span>
        <span>MIORPA NOTIFY</span>
    </div>

    <nav class="business-nav">
        <a href="{{ route('business.dashboard') }}">
            Inicio
        </a>

        <a
            href="{{ route('business.users.index') }}"
            class="{{ request()->routeIs('business.users.*') ? 'active' : '' }}"
        >
            Cajeros
        </a>
    </nav>

    <div class="business-user">
        <div class="business-user-data">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->business?->name }}</small>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="button button-secondary" type="submit">
                Salir
            </button>
        </form>
    </div>
</header>

<main class="business-main">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('business-content')
</main>
@endsection