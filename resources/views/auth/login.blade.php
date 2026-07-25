@extends('layouts.app')

@section('title', 'Ingresar | MIORPA NOTIFY')

@push('styles')
<style>
    .login-page {
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        min-height: 100vh;
        background: #ffffff;
    }

    /*
    |--------------------------------------------------------------------------
    | Panel de presentación
    |--------------------------------------------------------------------------
    */

    .login-presentation {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        padding: clamp(48px, 8vw, 130px);
        color: #0a2948;
        background:
            radial-gradient(
                circle at 10% 10%,
                rgba(255, 153, 0, .18),
                transparent 30%
            ),
            radial-gradient(
                circle at 90% 15%,
                rgba(20, 100, 210, .20),
                transparent 38%
            ),
            linear-gradient(
                145deg,
                #f8fbff 0%,
                #e7f2fb 48%,
                #d9f2ef 100%
            );
    }

    .login-presentation::before {
        position: absolute;
        top: -150px;
        right: -130px;
        width: 390px;
        height: 390px;
        border: 70px solid rgba(20, 100, 210, .06);
        border-radius: 50%;
        content: "";
    }

    .login-presentation::after {
        position: absolute;
        bottom: -190px;
        left: -160px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: rgba(255, 153, 0, .07);
        content: "";
    }

    .login-presentation > * {
        position: relative;
        z-index: 1;
    }

    /*
     * Logo del panel izquierdo.
     * Se muestra sin tarjeta ni fondo adicional.
     */
    .login-presentation .brand-logo {
        display: block;
        width: min(360px, 82%);
        height: auto;
        margin: 0;
        padding: 0;
        object-fit: contain;
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .login-presentation h1 {
        max-width: 640px;
        margin: 42px 0 20px;
        color: #092a47;
        font-size: clamp(40px, 5vw, 68px);
        line-height: 1.04;
        letter-spacing: -.03em;
    }

    .login-presentation p {
        max-width: 580px;
        margin: 0;
        color: #45647d;
        font-size: 18px;
        line-height: 1.65;
    }

    /*
    |--------------------------------------------------------------------------
    | Área del formulario
    |--------------------------------------------------------------------------
    */

    .login-area {
        display: grid;
        place-items: center;
        padding: clamp(28px, 5vw, 72px);
        background: #ffffff;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
    }

    /*
     * Logo ubicado encima del formulario.
     */
    .login-card .brand-logo {
        display: block;
        width: min(270px, 82%);
        height: auto;
        margin: 0 auto 34px;
        padding: 0;
        object-fit: contain;
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .login-card h2 {
        margin: 0 0 8px;
        color: #102a43;
        font-size: 32px;
        line-height: 1.2;
        text-align: center;
    }

    .login-card > p {
        margin: 0 0 32px;
        color: var(--muted);
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | Formulario
    |--------------------------------------------------------------------------
    */

    .login-form-group {
        display: grid;
        gap: 8px;
        margin-bottom: 18px;
    }

    .login-form-group label {
        color: #102a43;
        font-weight: 700;
    }

    .login-input {
        display: block;
        width: 100%;
        height: 58px;
        box-sizing: border-box;
        padding: 0 16px;
        color: #102a43;
        border: 1px solid #cbd9e6;
        border-radius: 12px;
        background: #f8fbff;
        font: inherit;
        font-size: 17px;
        outline: none;
        transition:
            border-color .18s ease,
            background .18s ease,
            box-shadow .18s ease;
    }

    .login-input:hover {
        border-color: #9eb6ca;
        background: #ffffff;
    }

    .login-input:focus {
        border-color: #1464d2;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(20, 100, 210, .12);
    }

    .login-password-field {
        position: relative;
        width: 100%;
    }

    .login-password-field .login-input {
        padding-right: 58px;
    }

    .login-password-toggle {
        position: absolute;
        z-index: 10;
        top: 50%;
        right: 9px;
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        padding: 0;
        transform: translateY(-50%);
        color: #36566f;
        border: 0;
        border-radius: 9px;
        background: transparent;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
    }

    .login-password-toggle:hover {
        background: #eaf2f9;
    }

    .login-password-toggle:focus-visible {
        outline: 2px solid #1464d2;
        outline-offset: 1px;
    }

    .field-error {
        color: #b42318;
        font-size: 14px;
    }

    .remember {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 4px 0 22px;
        color: var(--muted);
        font-size: 14px;
    }

    .remember input {
        width: 17px;
        height: 17px;
        accent-color: #1464d2;
    }

    .login-button {
        width: 100%;
        min-height: 52px;
        color: #ffffff;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(
            135deg,
            #1254b5 0%,
            #1464d2 65%,
            #0c81a2 100%
        );
        box-shadow: 0 10px 22px rgba(20, 100, 210, .18);
        cursor: pointer;
        font: inherit;
        font-weight: 700;
        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .login-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 13px 26px rgba(20, 100, 210, .25);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .login-button:focus-visible {
        outline: 3px solid rgba(255, 153, 0, .45);
        outline-offset: 3px;
    }

    .security-note {
        margin-top: 24px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | Diseño móvil
    |--------------------------------------------------------------------------
    */

    @media (max-width: 850px) {
        .login-page {
            grid-template-columns: 1fr;
        }

        .login-presentation {
            display: none;
        }

        .login-area {
            min-height: 100vh;
            box-sizing: border-box;
            padding: 32px 22px;
        }

        .login-card {
            max-width: 460px;
        }

        .login-card .brand-logo {
            width: min(250px, 78%);
            margin-bottom: 30px;
        }

        .login-card h2 {
            font-size: 28px;
        }
    }

    @media (max-width: 420px) {
        .login-area {
            padding: 28px 18px;
        }

        .login-card .brand-logo {
            width: min(220px, 76%);
        }

        .login-input {
            height: 54px;
            font-size: 16px;
        }
    }
</style>

@endpush

@section('content')
<main class="login-page">
    <section class="login-presentation">
        <div class="brand">
            <img
    src="{{ asset('logo.png') }}"
    alt="MIORPA Notify"
    class="brand-logo"
>

{{-- <span>MIORPA NOTIFY</span> --}}
        </div>

        <h1>Pagos en tiempo real, bajo tu control.</h1>

        <p>
            Recibe y administra los pagos de tus billeteras digitales
            únicamente desde dispositivos autorizados.
        </p>
    </section>

    <section class="login-area">
        <div class="login-card">
            <div class="brand">
               <img
    src="{{ asset('logo.png') }}"
    alt="MIORPA Notify"
    class="brand-logo"
>

{{-- <span>MIORPA NOTIFY</span> --}}
            </div>

            <h2>Bienvenido</h2>
            <p>Ingresa con tu cuenta para continuar.</p>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    Revisa los datos e inténtalo nuevamente.
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="login-form-group">
                    <label for="email">Correo electrónico</label>

                    <input
                        id="email"
                        class="login-input"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        autofocus
                    >

                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="login-form-group">
                    <label for="password">Contraseña</label>

                    <div class="login-password-field">
                        <input
                            id="password"
                            class="login-input"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            id="toggle-login-password"
                            class="login-password-toggle"
                            type="button"
                            aria-label="Mostrar contraseña"
                            title="Mostrar contraseña"
                        >
                            <span id="password-eye">👁</span>
                        </button>
                    </div>

                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <label class="remember">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
                    >
                    Mantener mi sesión iniciada
                </label>

                <button
                    class="button button-primary login-button"
                    type="submit"
                >
                    Ingresar
                </button>
            </form>

            <div class="security-note">
                Conexión protegida · Acceso exclusivo para usuarios autorizados
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('password');
        const button = document.getElementById('toggle-login-password');
        const eye = document.getElementById('password-eye');

        if (!input || !button || !eye) {
            return;
        }

        button.addEventListener('click', function () {
            const visible = input.type === 'text';

            input.type = visible ? 'password' : 'text';
            eye.textContent = visible ? '👁' : '🙈';

            button.setAttribute(
                'aria-label',
                visible
                    ? 'Mostrar contraseña'
                    : 'Ocultar contraseña'
            );

            button.setAttribute(
                'title',
                visible
                    ? 'Mostrar contraseña'
                    : 'Ocultar contraseña'
            );

            input.focus();
        });
    });
</script>
@endsection