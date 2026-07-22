@extends('superadmin.layout')

@section('title', 'Editar negocio | MIORPA NOTIFY')

@push('styles')
    <style>
        .form-panel {
            max-width: 780px;
            padding: 28px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: grid;
            gap: 7px;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 700;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            box-sizing: border-box;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font: inherit;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .field-error {
            color: #b42318;
            font-size: 14px;
        }

        @media (max-width: 680px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group-full {
                grid-column: auto;
            }
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 52px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
@endpush

@section('superadmin-content')
    <div class="page-heading">
        <div>
            <h1>Editar negocio</h1>
            <p>Actualiza la información de {{ $business->name }}.</p>
        </div>

        <a class="button button-secondary button-link" href="{{ route('superadmin.businesses.show', $business) }}">
            Cancelar
        </a>
    </div>

    <section class="panel form-panel">
        <form method="POST" action="{{ route('superadmin.businesses.update', $business) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group form-group-full">
                    <label for="name">Nombre comercial</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $business->name) }}" required>
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label for="legal_name">Razón social</label>
                    <input id="legal_name" name="legal_name" type="text"
                        value="{{ old('legal_name', $business->legal_name) }}">
                    @error('legal_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tax_id">RUC</label>
                    <input id="tax_id" name="tax_id" type="text" maxlength="11" inputmode="numeric"
                        value="{{ old('tax_id', $business->tax_id) }}">
                    @error('tax_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="contact_phone">Teléfono</label>
                    <input id="contact_phone" name="contact_phone" type="text" inputmode="tel"
                        value="{{ old('contact_phone', $business->contact_phone) }}">
                    @error('contact_phone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group form-group-full">
                    <label for="timezone">Zona horaria</label>
                    <select id="timezone" name="timezone" required>
                        <option value="America/Lima" @selected(old('timezone', $business->timezone) === 'America/Lima')>
                            Perú — America/Lima
                        </option>
                    </select>
                    @error('timezone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group form-group-full">
                    <h2>Acceso del administrador</h2>
                    <p>
                        La contraseña solamente cambiará si escribes una nueva.
                    </p>
                </div>

                <div class="form-group">
                    <label for="admin_name">Nombre del administrador</label>
                    <input id="admin_name" name="admin_name" type="text"
                        value="{{ old('admin_name', $administrator->name) }}" required>
                    @error('admin_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="admin_email">Correo de ingreso</label>
                    <input id="admin_email" name="admin_email" type="email"
                        value="{{ old('admin_email', $administrator->email) }}" autocomplete="email" required>
                    @error('admin_email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="admin_password">Nueva contraseña</label>

                    <div class="password-field">
                        <input id="admin_password" name="admin_password" type="password" autocomplete="new-password">

                        <button type="button" class="password-toggle" data-password-toggle="admin_password"
                            aria-label="Mostrar contraseña">
                            👁
                        </button>
                    </div>

                    @error('admin_password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="admin_password_confirmation">
                        Confirmar nueva contraseña
                    </label>

                    <div class="password-field">
                        <input id="admin_password_confirmation" name="admin_password_confirmation" type="password"
                            autocomplete="new-password">

                        <button type="button" class="password-toggle" data-password-toggle="admin_password_confirmation"
                            aria-label="Mostrar contraseña">
                            👁
                        </button>
                    </div>
                </div>


            </div>
            @include(
    'superadmin.businesses.subscription-fields',
    ['business' => $business]
)

            <div class="form-actions">
                <button class="button" type="submit">
                    Guardar cambios
                </button>

                <a class="button button-secondary button-link" href="{{ route('superadmin.businesses.show', $business) }}">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(
                    button.dataset.passwordToggle
                );

                const visible = input.type === 'text';

                input.type = visible ? 'password' : 'text';
                button.textContent = visible ? '👁' : '🙈';
            });
        });
    </script>
@endpush