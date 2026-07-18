@extends('business.layout')

@section('title', 'Editar cajero | MIORPA NOTIFY')

@include('business.users.styles')

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Editar cajero</h1>
        <p>Actualiza los datos y acceso de {{ $user->name }}.</p>
    </div>
</div>

<section class="panel user-form-panel">
    <form
        method="POST"
        action="{{ route('business.users.update', $user) }}"
    >
        @csrf
        @method('PUT')

        @include('business.users.form')

        <div class="form-actions">
            <button class="button" type="submit">
                Guardar cambios
            </button>

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