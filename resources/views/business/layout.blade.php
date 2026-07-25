@extends('layouts.app')

@push('styles')

    <style>
        /*
                                    |--------------------------------------------------------------------------
                                    | Barra principal
                                    |--------------------------------------------------------------------------
                                    */

        .business-header {
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
                radial-gradient(circle at 12% 0%,
                    rgba(20, 100, 210, .20),
                    transparent 32%),
                linear-gradient(110deg,
                    #071f36 0%,
                    #092f4e 52%,
                    #0b485d 100%);
            box-shadow: 0 8px 24px rgba(7, 31, 54, .13);
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Logo
                                    |--------------------------------------------------------------------------
                                    */

        .business-header .brand {
            display: inline-flex;
            align-items: center;
            justify-self: start;
            min-width: 0;
            color: white;
            text-decoration: none;
        }

        .brand-logo {
            display: block;
            width: clamp(175px, 14vw, 220px);
            max-height: 54px;
            padding: 0;
            object-fit: contain;
            object-position: left center;
            background: transparent;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        /*
                                     * Oculta el nombre separado porque ya está incluido
                                     * dentro del nuevo logo.
                                     */
        .business-header .brand>span:not(.brand-mark) {
            display: none;
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Navegación de escritorio
                                    |--------------------------------------------------------------------------
                                    */

        .business-nav-desktop {
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

        .business-nav-desktop a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            box-sizing: border-box;
            padding: 0 16px;
            color: #d8e7f4;
            border-radius: 9px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition:
                color .18s ease,
                background .18s ease,
                box-shadow .18s ease,
                transform .18s ease;
        }

        .business-nav-desktop a:hover {
            color: white;
            background: rgba(255, 255, 255, .10);
            transform: translateY(-1px);
        }

        .business-nav-desktop a.active {
            color: white;
            background: linear-gradient(135deg,
                    rgba(20, 100, 210, .85),
                    rgba(12, 129, 162, .85));
            box-shadow: 0 5px 14px rgba(0, 0, 0, .15);
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Usuario
                                    |--------------------------------------------------------------------------
                                    */

        .business-user {
            display: flex;
            align-items: center;
            justify-self: end;
            gap: 12px;
        }

        .business-user-data {
            position: relative;
            min-width: 0;
            padding-left: 43px;
            text-align: right;
        }

        .business-user-data::before {
            position: absolute;
            top: 50%;
            left: 0;
            display: grid;
            width: 35px;
            height: 35px;
            place-items: center;
            transform: translateY(-50%);
            color: white;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 50%;
            background: linear-gradient(135deg,
                    #1464d2,
                    #0c81a2);
            content: "MN";
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .04em;
        }

        .business-user-data strong,
        .business-user-data small {
            display: block;
        }

        .business-user-data strong {
            max-width: 215px;
            overflow: hidden;
            color: white;
            font-size: 14px;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .business-user-data small {
            max-width: 215px;
            margin-top: 3px;
            overflow: hidden;
            color: #bdd7e8;
            font-size: 12px;
            line-height: 1.2;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .business-user form {
            margin: 0;
        }

        .business-user .button {
            min-height: 42px;
            box-sizing: border-box;
            padding: 0 18px;
            color: #0a3453;
            border: 1px solid rgba(255, 255, 255, .60);
            border-radius: 11px;
            background: #f3f8fc;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .10);
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            transition:
                color .18s ease,
                border-color .18s ease,
                background .18s ease,
                transform .18s ease;
        }

        .business-user .button:hover {
            color: white;
            border-color: #f79300;
            background: #f79300;
            transform: translateY(-1px);
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Contenido general
                                    |--------------------------------------------------------------------------
                                    */

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
            box-shadow: 0 8px 22px rgba(11, 45, 79, .04);
        }

        .button-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Navegación móvil
                                    |--------------------------------------------------------------------------
                                    */

        .business-mobile-nav {
            display: none;
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Tabletas
                                    |--------------------------------------------------------------------------
                                    */

        @media (max-width: 1050px) {
            .business-header {
                grid-template-columns: auto 1fr auto;
                padding-right: 24px;
                padding-left: 24px;
            }

            .brand-logo {
                width: 165px;
            }

            .business-nav-desktop a {
                padding: 0 11px;
                font-size: 14px;
            }

            .business-user-data::before {
                display: none;
            }

            .business-user-data {
                padding-left: 0;
            }
        }

        /*
                                    |--------------------------------------------------------------------------
                                    | Celulares
                                    |--------------------------------------------------------------------------
                                    */

        @media (max-width: 900px) {
            .business-header {
                position: relative;
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 12px;
                min-height: 70px;
                padding: 11px 18px;
            }

            .business-nav-desktop,
            .business-user-data {
                display: none;
            }

            .business-header .brand {
                min-width: 0;
            }

            .brand-logo {
                width: min(175px, 54vw);
                max-height: 47px;
            }

            .business-user {
                flex-shrink: 0;
            }

            .business-user .button {
                min-height: 40px;
                padding: 0 14px;
                font-size: 13px;
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
                border: 1px solid rgba(255, 255, 255, .14);
                border-radius: 17px;
                background:
                    linear-gradient(110deg,
                        #071f36,
                        #0a3d57);
                box-shadow: 0 14px 35px rgba(11, 45, 79, .35);
            }

            .business-mobile-nav a {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 0;
                min-height: 49px;
                padding: 7px 5px;
                color: #d8e7f4;
                border-radius: 11px;
                text-align: center;
                text-decoration: none;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.2;
                transition:
                    color .18s ease,
                    background .18s ease;
            }

            .business-mobile-nav a:hover {
                color: white;
                background: rgba(255, 255, 255, .09);
            }

            .business-mobile-nav a.active {
                color: white;
                background: linear-gradient(135deg,
                        rgba(20, 100, 210, .90),
                        rgba(12, 129, 162, .90));
            }
        }

        @media (max-width: 390px) {
            .business-header {
                padding: 10px 12px;
            }

            .brand-logo {
                width: min(148px, 50vw);
                max-height: 43px;
            }

            .business-user .button {
                min-height: 38px;
                padding: 0 11px;
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
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

        <meta name="theme-color" content="#092f4e">
        <link rel="icon" type="image/png" href="{{ asset('logo-icon-192.png') }}">

        <link rel="apple-touch-icon" href="{{ asset('logo-icon-192.png') }}">

        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <div class="brand">
            <img src="{{ asset('logo.png') }}" alt="MIORPA Notify" class="brand-logo">


        </div>

        <nav class="business-nav-desktop" aria-label="Navegación principal">
            @if (auth()->user()->isAdministrator())
                    <a href="{{ route(
                    'business.dashboard'
                ) }}" class="{{ request()->routeIs(
                    'business.dashboard'
                ) ? 'active' : '' }}">
                        Inicio
                    </a>

                    <a href="{{ route(
                    'business.users.index'
                ) }}" class="{{ request()->routeIs(
                    'business.users.*'
                ) ? 'active' : '' }}">
                        Cajeros
                    </a>

                    <a href="{{ route(
                    'business.devices.index'
                ) }}" class="{{ request()->routeIs(
                    'business.devices.*'
                ) ? 'active' : '' }}">
                        Dispositivos
                    </a>
            @endif

            <a href="{{ route(
        'business.payments.index'
    ) }}" class="{{ request()->routeIs(
        'business.payments.*'
    ) ? 'active' : '' }}">
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
            <div class="alert {{
                $subscriptionWarning['level']
                === 'danger'
                ? 'alert-danger'
                : 'alert-warning'
                                                                            }}">
                {{ $subscriptionWarning['message'] }}
            </div>
        @endif

        @yield('business-content')
    </main>

    <nav class="business-mobile-nav" aria-label="Navegación móvil">
        @if (auth()->user()->isAdministrator())
            <a href="{{ route(
                'business.dashboard'
            ) }}" class="{{ request()->routeIs(
                'business.dashboard'
            ) ? 'active' : '' }}">
                Inicio
            </a>

            <a href="{{ route(
                'business.users.index'
            ) }}" class="{{ request()->routeIs(
                'business.users.*'
            ) ? 'active' : '' }}">
                Cajeros
            </a>

            <a href="{{ route(
                'business.devices.index'
            ) }}" class="{{ request()->routeIs(
                'business.devices.*'
            ) ? 'active' : '' }}">
                Dispositivos
            </a>
        @endif

        <a href="{{ route(
        'business.payments.index'
    ) }}" class="{{ request()->routeIs(
        'business.payments.*'
    ) ? 'active' : '' }}">
            Pagos
        </a>


        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register(
                        '{{ asset('sw.js') }}'
                    ).then(function (registration) {
                        console.log(
                            'Miorpa Notify PWA activa',
                            registration.scope
                        );
                    }).catch(function (error) {
                        console.error(
                            'No se pudo registrar la PWA:',
                            error
                        );
                    });
                });
            }
        </script>
    </nav>
@endsection