@extends('layouts.app')

@section('title', 'Ingresar | MIORPA NOTIFY')

@push('styles')
<style>
    .login-page {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        min-height: 100vh;
    }

    .login-presentation {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 8vw;
        color: white;
        background:
            radial-gradient(
                circle at top right,
                rgba(19, 168, 158, .7),
                transparent 35%
            ),
            linear-gradient(145deg, #0a2948, #123a63);
    }

    .login-presentation h1 {
        max-width: 620px;
        margin: 32px 0 18px;
        font-size: clamp(38px, 5vw, 68px);
        line-height: 1.02;
    }

    .login-presentation p {
        max-width: 560px;
        color: #d8e7f4;
        font-size: 18px;
        line-height: 1.6;
    }

    .login-area {
        display: grid;
        place-items: center;
        padding: 28px;
        background: var(--surface);
    }

    .login-card {
        width: 100%;
        max-width: 430px;
    }

    .login-card h2 {
        margin: 28px 0 8px;
        font-size: 30px;
    }

    .login-card > p {
        margin: 0 0 28px;
        color: var(--muted);
    }

    .login-form-group {
        display: grid;
        gap: 8px;
        margin-bottom: 18px;
    }

    .login-form-group label {
        font-weight: 700;
    }

    .login-input {
        display: block;
        width: 100%;
        height: 58px;
        box-sizing: border-box;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: white;
        font: inherit;
        font-size: 17px;
        outline: none;
    }

    .login-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(18, 91, 135, .12);
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
        width: 42px;
        height: 42px;
        padding: 0;
        transform: translateY(-50%);
        border: 0;
        border-radius: 9px;
        background: transparent;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
    }

    .login-password-toggle:hover {
        background: #edf3f8;
    }

    .login-password-toggle:focus-visible {
        outline: 2px solid var(--primary);
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
        margin: 4px 0 20px;
        color: var(--muted);
        font-size: 14px;
    }

    .remember input {
        width: 16px;
        height: 16px;
    }

    .login-button {
        width: 100%;
    }

    .security-note {
        margin-top: 22px;
        color: var(--muted);
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 850px) {
        .login-page {
            grid-template-columns: 1fr;
        }

        .login-presentation {
            display: none;
        }

        .login-area {
            padding: 24px;
        }
    }
</style>
@endpush

@section('content')
<main class="login-page">
    <section class="login-presentation">
        <div class="brand">
            <span class="brand-mark">MN</span>
            <span>MIORPA NOTIFY</span>
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
                <span class="brand-mark">MN</span>
                <span>MIORPA NOTIFY</span>
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