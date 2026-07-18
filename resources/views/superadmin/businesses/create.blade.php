@extends('superadmin.layout')

@section('title', 'Crear negocio | MIORPA NOTIFY')

@push('styles')
<style>
    .form-panel {
        max-width: 900px;
        padding: 28px;
    }

    .form-section {
        margin-bottom: 32px;
    }

    .form-section h2 {
        margin: 0 0 6px;
        font-size: 20px;
    }

    .form-section > p {
        margin: 0 0 22px;
        color: var(--muted);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 22px;
        border-top: 1px solid var(--border);
    }

    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('superadmin-content')
<div class="page-heading">
    <div>
        <h1>Crear negocio</h1>
        <p>Registra al cliente y su administrador principal.</p>
    </div>
</div>

<form
    class="panel form-panel"
    method="POST"
    action="{{ route('superadmin.businesses.store') }}"
>
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            Revisa los campos marcados antes de continuar.
        </div>
    @endif

    <section class="form-section">
        <h2>Información del negocio</h2>
        <p>Datos principales del cliente.</p>

        <div class="form-grid">
            <div class="field">
                <label for="name">Nombre comercial *</label>
                <input id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="legal_name">Razón social</label>
                <input id="legal_name" name="legal_name" value="{{ old('legal_name') }}">
                @error('legal_name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="tax_id">RUC</label>
                <input id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                @error('tax_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="contact_phone">Teléfono del negocio</label>
                <input id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                @error('contact_phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <h2>Administrador principal</h2>
        <p>Esta persona administrará únicamente su negocio.</p>

        <div class="form-grid">
            <div class="field">
                <label for="admin_name">Nombre completo *</label>
                <input id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                @error('admin_name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="admin_email">Correo *</label>
                <input
                    id="admin_email"
                    name="admin_email"
                    type="email"
                    value="{{ old('admin_email') }}"
                    required
                >
                @error('admin_email')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="admin_phone">Teléfono</label>
                <input id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}">
                @error('admin_phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div></div>

            <div class="field">
                <label for="admin_password">Contraseña temporal *</label>
                <input
                    id="admin_password"
                    name="admin_password"
                    type="password"
                    required
                >
                @error('admin_password')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="admin_password_confirmation">Confirmar contraseña *</label>
                <input
                    id="admin_password_confirmation"
                    name="admin_password_confirmation"
                    type="password"
                    required
                >
            </div>
        </div>
    </section>

    <div class="form-actions">
        <a
            class="button button-secondary button-link"
            href="{{ route('superadmin.businesses.index') }}"
        >
            Cancelar
        </a>

        <button class="button button-primary" type="submit">
            Crear negocio
        </button>
    </div>
</form>
@endsection