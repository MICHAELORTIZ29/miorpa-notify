@extends('layouts.app')

@push('styles')

<style>
    .admin-header {
        position: sticky;
        z-index: 100;
        top: 0;
        display: grid;
        grid-template-columns: minmax(230px, 1fr) auto minmax(230px, 1fr);
        align-items: center;
        gap: 22px;
        min-height: 78px;
        box-sizing: border-box;
        padding: 12px clamp(24px, 4vw, 76px);
        color: white;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        background:
            radial-gradient(
                circle at 12% 0%,
                rgba(20, 100, 210, .20),
                transparent 32%
            ),
            linear-gradient(
                110deg,
                #071f36 0%,
                #092f4e 52%,
                #0b485d 100%
            );
        box-shadow: 0 8px 24px rgba(7, 31, 54, .13);
    }

    .admin-header .brand {
        display: inline-flex;
        align-items: center;
        justify-self: start;
        min-width: 0;
        color: white;
        text-decoration: none;
    }

    .admin-header .brand-logo {
        display: block;
        width: clamp(175px, 14vw, 220px);
        max-height: 54px;
        object-fit: contain;
        object-position: left center;
        background: transparent;
        border: 0;
        border-radius: 0;
    }

    .admin-header .brand > span:not(.brand-mark) {
        display: none;
    }

    .admin-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        justify-self: center;
        gap: 6px;
        padding: 5px;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 13px;
        background: rgba(255, 255, 255, .045);
    }

    .admin-nav a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 17px;
        color: #d8e7f4;
        border-radius: 9px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        transition:
            color .18s ease,
            background .18s ease,
            transform .18s ease;
    }

    .admin-nav a:hover {
        color: white;
        background: rgba(255, 255, 255, .10);
        transform: translateY(-1px);
    }

    .admin-nav a.active {
        color: white;
        background: linear-gradient(
            135deg,
            rgba(20, 100, 210, .88),
            rgba(12, 129, 162, .88)
        );
        box-shadow: 0 5px 14px rgba(0, 0, 0, .15);
    }

    .admin-user {
        display: flex;
        align-items: center;
        justify-self: end;
        gap: 12px;
    }

    .admin-user-data {
        position: relative;
        padding-left: 43px;
        text-align: right;
    }

    .admin-user-data::before {
        position: absolute;
        top: 50%;
        left: 0;
        display: grid;
        width: 35px;
        height: 35px;
        place-items: center;
        transform: translateY(-50%);
        color: white;
        border-radius: 50%;
        background: linear-gradient(
            135deg,
            #1464d2,
            #0c81a2
        );
        content: "MN";
        font-size: 10px;
        font-weight: 800;
    }

    .admin-user-data strong,
    .admin-user-data small {
        display: block;
    }

    .admin-user-data strong {
        color: white;
        font-size: 14px;
    }

    .admin-user-data small {
        margin-top: 3px;
        color: #bdd7e8;
        font-size: 12px;
    }

    .admin-user form {
        margin: 0;
    }

    .admin-user .button {
        min-height: 42px;
        padding: 0 18px;
        color: #0a3453;
        border: 1px solid rgba(255, 255, 255, .60);
        border-radius: 11px;
        background: #f3f8fc;
        cursor: pointer;
        font-weight: 700;
        transition:
            color .18s ease,
            background .18s ease,
            transform .18s ease;
    }

    .admin-user .button:hover {
        color: white;
        background: #f79300;
        transform: translateY(-1px);
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
        color: #102a43;
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

    @media (max-width: 1050px) {
        .admin-header {
            grid-template-columns: auto 1fr auto;
            padding-right: 24px;
            padding-left: 24px;
        }

        .admin-header .brand-logo {
            width: 165px;
        }

        .admin-nav a {
            padding: 0 11px;
            font-size: 14px;
        }

        .admin-user-data::before {
            display: none;
        }

        .admin-user-data {
            padding-left: 0;
        }
    }

    @media (max-width: 850px) {
        .admin-header {
            position: relative;
            grid-template-columns: minmax(0, 1fr) auto;
            min-height: 70px;
            padding: 11px 18px;
        }

        .admin-nav,
        .admin-user-data {
            display: none;
        }

        .admin-header .brand-logo {
            width: min(175px, 55vw);
            max-height: 47px;
        }

        .admin-user .button {
            min-height: 40px;
            padding: 0 14px;
            font-size: 13px;
        }

        .admin-main {
            width: min(100% - 28px, 760px);
            margin: 25px auto 40px;
        }

        .page-heading {
            align-items: stretch;
            flex-direction: column;
        }
    }

    @media (max-width: 390px) {
        .admin-header {
            padding: 10px 12px;
        }

        .admin-header .brand-logo {
            width: min(148px, 50vw);
        }

        .admin-user .button {
            padding: 0 11px;
            font-size: 12px;
        }
    }
</style>

@endpush

@section('content')
<header class="admin-header">
    <div class="brand">
       <img
    src="{{ asset('logo.png') }}"
    alt="MIORPA Notify"
    class="brand-logo"
>

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