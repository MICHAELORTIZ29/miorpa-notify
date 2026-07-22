@extends('layouts.app')

@section('title', 'Suscripción suspendida | MIORPA NOTIFY')

@section('content')
<div style="
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 24px;
    background: #f3f6fa;
">
    <section style="
        width: min(560px, 100%);
        padding: 32px;
        text-align: center;
        background: white;
        border: 1px solid #d9e2ec;
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(18, 58, 99, .08);
    ">
        <div style="
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            color: white;
            font-size: 30px;
            background: #c62828;
            border-radius: 50%;
        ">
            !
        </div>

        <h1 style="margin: 0 0 12px; color: #123a63;">
            Suscripción suspendida
        </h1>

        <p style="margin: 0 0 18px; color: #526581; line-height: 1.6;">
            El acceso operativo de este negocio se encuentra temporalmente
            suspendido.
        </p>

        <p style="margin: 0 0 24px; color: #526581; line-height: 1.6;">
            Verifica el estado de tu plan o comunícate con el administrador
            de MIORPA NOTIFY para regularizar el servicio.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                style="
                    padding: 12px 20px;
                    color: white;
                    font-weight: 700;
                    background: #123a63;
                    border: 0;
                    border-radius: 10px;
                    cursor: pointer;
                "
            >
                Cerrar sesión
            </button>
        </form>
    </section>
</div>
@endsection