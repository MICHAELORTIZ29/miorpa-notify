@php
    $editing = isset($user);
@endphp

<div class="form-grid">
    <div class="form-group form-group-full">
        <label for="name">Nombre completo</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $user->name ?? '') }}"
            required
        >
        @error('name')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group form-group-full">
        <label for="email">Correo de ingreso</label>
        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $user->email ?? '') }}"
            required
        >
        @error('email')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    @unless ($editing)
        <input name="role_code" type="hidden" value="cashier">
    @endunless

    <div class="form-group">
        <label for="password">
            {{ $editing ? 'Nueva contraseña' : 'Contraseña' }}
        </label>

        <div class="password-field">
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                @required(! $editing)
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password"
            >
                👁
            </button>
        </div>

        @if ($editing)
            <small>Déjala vacía para mantener la contraseña actual.</small>
        @endif

        @error('password')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">
            Confirmar contraseña
        </label>

        <div class="password-field">
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                @required(! $editing)
            >

            <button
                type="button"
                class="password-toggle"
                data-password-toggle="password_confirmation"
            >
                👁
            </button>
        </div>
    </div>
</div>
