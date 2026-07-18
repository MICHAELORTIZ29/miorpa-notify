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
            radial-gradient(circle at top right, rgba(19, 168, 158, .7), transparent 35%),
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

    .remember {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 20px;
        color: var(--muted);
        font-size: 14px;
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

                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        autofocus
                    >

                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >

                    @error('password')
                        <div class="field-error">{{ $message }}</div>
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

                <button class="button button-primary login-button" type="submit">
                    Ingresar
                </button>
            </form>

            <div class="security-note">
                Conexión protegida · Acceso exclusivo para usuarios autorizados
            </div>
        </div>
    </section>
</main>
@endsection