@extends('layouts.app')

@push('styles')
<style>
    .business-header {
        position: relative;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 16px 4vw;
        color: white;
        background: var(--primary-dark);
    }

    .business-nav-desktop {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .business-nav-desktop a {
        padding: 10px 14px;
        color: #d8e7f4;
        text-decoration: none;
        border-radius: 9px;
    }

    .business-nav-desktop a:hover,
    .business-nav-desktop a.active {
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

    .business-user-data strong,
    .business-user-data small {
        display: block;
    }

    .business-user-data small {
        color: #bdd0e2;
    }

    .business-main {
        width: min(1240px, 92%);
        margin: 34px auto;
    }

    .page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
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

    /*
     * Navegación móvil.
     */
    .business-mobile-nav {
        display: none;
    }

    @media (max-width: 900px) {
        .business-header {
            padding: 13px 18px;
        }

        .business-nav-desktop,
        .business-user-data {
            display: none;
        }

        .business-main {
            width: min(100% - 28px, 760px);
            margin: 25px auto 105px;
        }

        .page-heading {
            align-items: stretch;
            flex-direction: column;
        }

        .business-mobile-nav {
            position: fixed;
            z-index: 1000;
            right: 12px;
            bottom: 12px;
            left: 12px;
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 1fr;
            gap: 5px;
            padding: 7px;
            background: var(--primary-dark);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 17px;
            box-shadow:
                0 14px 35px rgba(11, 45, 79, .35);
        }

        .business-mobile-nav a {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            min-height: 49px;
            padding: 7px 5px;
            color: #d8e7f4;
            text-align: center;
            text-decoration: none;
            border-radius: 11px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
        }

        .business-mobile-nav a.active {
            color: white;
            background: rgba(255, 255, 255, .16);
        }

        .business-header .brand {
            min-width: 0;
            font-size: 14px;
        }

        .business-header .brand-mark {
            flex-shrink: 0;
        }

        .business-user {
            flex-shrink: 0;
        }

        .business-user .button {
            padding: 10px 13px;
            font-size: 13px;
        }
    }

    @media (max-width: 390px) {
        .business-header {
            padding: 12px;
        }

        .business-header .brand {
            font-size: 12px;
        }

        .business-header .brand-mark {
            width: 35px;
            height: 35px;
            font-size: 13px;
        }

        .business-user .button {
            padding: 9px 11px;
            font-size: 12px;
        }

        .business-mobile-nav {
            right: 7px;
            bottom: 7px;
            left: 7px;
        }

        .business-mobile-nav a {
            font-size: 11px;
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

    <nav
        class="business-nav-desktop"
        aria-label="Navegación principal"
    >
        @if (auth()->user()->isAdministrator())
            <a
                href="{{ route(
                    'business.dashboard'
                ) }}"
                class="{{ request()->routeIs(
                    'business.dashboard'
                ) ? 'active' : '' }}"
            >
                Inicio
            </a>

            <a
                href="{{ route(
                    'business.users.index'
                ) }}"
                class="{{ request()->routeIs(
                    'business.users.*'
                ) ? 'active' : '' }}"
            >
                Cajeros
            </a>

            <a
                href="{{ route(
                    'business.devices.index'
                ) }}"
                class="{{ request()->routeIs(
                    'business.devices.*'
                ) ? 'active' : '' }}"
            >
                Dispositivos
            </a>
        @endif

        <a
            href="{{ route(
                'business.payments.index'
            ) }}"
            class="{{ request()->routeIs(
                'business.payments.*'
            ) ? 'active' : '' }}"
        >
            Pagos
        </a>
    </nav>

    <div class="business-user">
        <div class="business-user-data">
            <strong>
                {{ auth()->user()->name }}
            </strong>

            <small>
                {{ auth()->user()->business?->name }}
            </small>
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
        >
            @csrf

            <button
                class="button button-secondary"
                type="submit"
            >
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

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @php
        $subscriptionWarning = auth()
            ->user()
            ->business
            ?->currentSubscription
            ?->warning();
    @endphp

    @if ($subscriptionWarning)
        <div
            class="alert {{
                $subscriptionWarning['level']
                    === 'danger'
                        ? 'alert-danger'
                        : 'alert-warning'
            }}"
        >
            {{ $subscriptionWarning['message'] }}
        </div>
    @endif

    @yield('business-content')
</main>

<nav
    class="business-mobile-nav"
    aria-label="Navegación móvil"
>
    @if (auth()->user()->isAdministrator())
        <a
            href="{{ route(
                'business.dashboard'
            ) }}"
            class="{{ request()->routeIs(
                'business.dashboard'
            ) ? 'active' : '' }}"
        >
            Inicio
        </a>

        <a
            href="{{ route(
                'business.users.index'
            ) }}"
            class="{{ request()->routeIs(
                'business.users.*'
            ) ? 'active' : '' }}"
        >
            Cajeros
        </a>

        <a
            href="{{ route(
                'business.devices.index'
            ) }}"
            class="{{ request()->routeIs(
                'business.devices.*'
            ) ? 'active' : '' }}"
        >
            Dispositivos
        </a>
    @endif

    <a
        href="{{ route(
            'business.payments.index'
        ) }}"
        class="{{ request()->routeIs(
            'business.payments.*'
        ) ? 'active' : '' }}"
    >
        Pagos
    </a>
</nav>
@endsection