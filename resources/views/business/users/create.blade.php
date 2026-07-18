@extends('business.layout')

@section('title', 'Crear cajero | MIORPA NOTIFY')

@include('business.users.styles')

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Crear cajero</h1>
        <p>Crea un acceso independiente para un cajero.</p>
    </div>
</div>

<section class="panel user-form-panel">
    <form method="POST" action="{{ route('business.users.store') }}">
        @csrf

        @include('business.users.form')

        <div class="form-actions">
            <button class="button" type="submit">Crear cajero</button>

            <a
                class="button button-secondary button-link"
                href="{{ route('business.users.index') }}"
            >
                Cancelar
            </a>
        </div>
    </form>
</section>
@endsection

@include('business.users.scripts')
