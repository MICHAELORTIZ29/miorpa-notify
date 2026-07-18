@extends('business.layout')

@section('title', 'Dispositivos | MIORPA NOTIFY')

@push('styles')
<style>
    .device-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 25px;
    }

    .summary-card {
        padding: 22px;
    }

    .summary-card span {
        color: var(--muted);
    }

    .summary-card strong {
        display: block;
        margin-top: 8px;
        font-size: 32px;
    }

    .device-layout {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 22px;
        align-items: start;
    }

    .form-card,
    .codes-card,
    .devices-card {
        padding: 24px;
    }

    .form-card h2,
    .codes-card h2,
    .devices-card h2 {
        margin-top: 0;
    }

    .pairing-form {
        display: grid;
        gap: 17px;
    }

    .form-group {
        display: grid;
        gap: 7px;
    }

    .form-group label {
        font-weight: 700;
    }

    .form-group select {
        width: 100%;
        box-sizing: border-box;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: white;
        font: inherit;
    }

    .new-code {
        margin-bottom: 22px;
        padding: 22px;
        border: 2px solid #0f8e88;
        border-radius: 14px;
        background: #effcf9;
        text-align: center;
    }

    .new-code small {
        display: block;
        color: var(--muted);
    }

    .new-code-value {
        display: block;
        margin: 12px 0;
        color: #08365e;
        font-family: monospace;
        font-size: clamp(24px, 4vw, 38px);
        letter-spacing: 2px;
    }

    .copy-message {
        min-height: 20px;
        margin-top: 8px;
        color: #08783e;
        font-size: 14px;
    }

    .codes-table,
    .devices-table {
        width: 100%;
        border-collapse: collapse;
    }

    .codes-table th,
    .codes-table td,
    .devices-table th,
    .devices-table td {
        padding: 13px;
        text-align: left;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-active {
        color: #08783e;
        background: #e9f9ef;
    }

    .status-disabled,
    .status-expired {
        color: #92400e;
        background: #fff4df;
    }

    .status-revoked {
        color: #b42318;
        background: #feeceb;
    }

    .status-used {
        color: #175cd3;
        background: #eaf2ff;
    }

    .table-actions {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .table-actions form {
        margin: 0;
    }

    .danger-button {
        border: 0;
        background: #b42318;
        color: white;
        cursor: pointer;
    }

    .empty-state {
        padding: 35px;
        color: var(--muted);
        text-align: center;
    }

    .devices-card {
        margin-top: 22px;
    }

    @media (max-width: 900px) {
        .device-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 680px) {
        .device-summary {
            grid-template-columns: 1fr;
        }

        .table-scroll {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Dispositivos</h1>
        <p>Vincula y administra los dispositivos autorizados.</p>
    </div>
</div>

<section class="device-summary">
    <article class="panel summary-card">
        <span>Dispositivos activos</span>
        <strong>{{ $activeDevices }}</strong>
    </article>

    <article class="panel summary-card">
        <span>Emisores Android</span>
        <strong>{{ $emitterDevices }}</strong>
    </article>

    <article class="panel summary-card">
        <span>Receptores</span>
        <strong>{{ $receiverDevices }}</strong>
    </article>
</section>

@if (session('new_pairing_code'))
    <section class="new-code">
        <small>Código temporal generado</small>

        <strong
            id="new-pairing-code"
            class="new-code-value"
        >
            {{ session('new_pairing_code') }}
        </strong>

        <button
            id="copy-pairing-code"
            class="button"
            type="button"
        >
            Copiar código
        </button>

        <div id="copy-message" class="copy-message"></div>

        <small>
            Vence: {{ session('pairing_code_expires_at') }}.
            Por seguridad, este código completo solo se muestra ahora.
        </small>
    </section>
@endif

<section class="device-layout">
    <article class="panel form-card">
        <h2>Generar vinculación</h2>

        <p>
            Crea un código temporal para autorizar un nuevo dispositivo.
        </p>

        <form
            class="pairing-form"
            method="POST"
            action="{{ route('business.devices.pairing-codes.store') }}"
        >
            @csrf

            <div class="form-group">
                <label for="device_type">Tipo de dispositivo</label>

                <select id="device_type" name="device_type" required>
                    <option value="">Seleccionar</option>

                    <option
                        value="emitter"
                        @selected(old('device_type') === 'emitter')
                    >
                        Emisor Android
                    </option>

                    <option
                        value="receiver"
                        @selected(old('device_type') === 'receiver')
                    >
                        Receptor web
                    </option>
                </select>

                @error('device_type')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="valid_minutes">Duración</label>

                <select id="valid_minutes" name="valid_minutes" required>
                    <option value="5">5 minutos</option>
                    <option value="10" selected>10 minutos</option>
                    <option value="15">15 minutos</option>
                    <option value="30">30 minutos</option>
                </select>

                @error('valid_minutes')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <button class="button" type="submit">
                Generar código
            </button>
        </form>
    </article>

    <article class="panel codes-card">
        <h2>Códigos recientes</h2>

        <div class="table-scroll">
            <table class="codes-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pairingCodes as $pairingCode)
                        @php
                            if ($pairingCode->revoked_at) {
                                $codeStatus = 'revoked';
                                $codeStatusLabel = 'Revocado';
                            } elseif ($pairingCode->used_at) {
                                $codeStatus = 'used';
                                $codeStatusLabel = 'Utilizado';
                            } elseif ($pairingCode->expires_at->isPast()) {
                                $codeStatus = 'expired';
                                $codeStatusLabel = 'Vencido';
                            } else {
                                $codeStatus = 'active';
                                $codeStatusLabel = 'Disponible';
                            }
                        @endphp

                        <tr>
                            <td>••••-{{ $pairingCode->code_suffix }}</td>

                            <td>
                                {{ $pairingCode->device_type === 'emitter'
                                    ? 'Emisor'
                                    : 'Receptor' }}
                            </td>

                            <td>
                                {{ $pairingCode->expires_at
                                    ->timezone('America/Lima')
                                    ->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <span class="status status-{{ $codeStatus }}">
                                    {{ $codeStatusLabel }}
                                </span>
                            </td>

                            <td>
                                @if ($pairingCode->isUsable())
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'business.devices.pairing-codes.revoke',
                                            $pairingCode
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="button button-secondary"
                                            type="submit"
                                            onclick="return confirm(
                                                '¿Deseas revocar este código?'
                                            )"
                                        >
                                            Revocar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                No se han generado códigos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="panel devices-card">
    <h2>Dispositivos vinculados</h2>

    <div class="table-scroll">
        <table class="devices-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Plataforma</th>
                    <th>Última conexión</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($devices as $device)
                    <tr>
                        <td>
                            <strong>{{ $device->name }}</strong>
                            <br>
                            <small>{{ $device->app_version ?: 'Sin versión' }}</small>
                        </td>

                        <td>
                            {{ $device->type === 'emitter'
                                ? 'Emisor'
                                : 'Receptor' }}
                        </td>

                        <td>{{ ucfirst($device->platform) }}</td>

                        <td>
                            {{ $device->last_seen_at
                                ? $device->last_seen_at
                                    ->timezone('America/Lima')
                                    ->diffForHumans()
                                : 'Nunca' }}
                        </td>

                        <td>
                            <span class="status status-{{ $device->status }}">
                                @switch($device->status)
                                    @case('active')
                                        Activo
                                        @break
                                    @case('disabled')
                                        Desactivado
                                        @break
                                    @case('revoked')
                                        Revocado
                                        @break
                                @endswitch
                            </span>
                        </td>

                        <td>
                            <div class="table-actions">
                                @if ($device->status === 'active')
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'business.devices.deactivate',
                                            $device
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="button button-secondary"
                                            type="submit"
                                        >
                                            Desactivar
                                        </button>
                                    </form>
                                @elseif ($device->status === 'disabled')
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'business.devices.activate',
                                            $device
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="button" type="submit">
                                            Activar
                                        </button>
                                    </form>
                                @endif

                                @if ($device->status !== 'revoked')
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'business.devices.revoke',
                                            $device
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="button danger-button"
                                            type="submit"
                                            onclick="return confirm(
                                                'Esta acción desvinculará el dispositivo. ¿Continuar?'
                                            )"
                                        >
                                            Desvincular
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Todavía no hay dispositivos vinculados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $devices->links() }}
</section>

@if (session('new_pairing_code'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('copy-pairing-code');
        const code = document.getElementById('new-pairing-code');
        const message = document.getElementById('copy-message');

        button?.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(
                    code.textContent.trim()
                );

                message.textContent = 'Código copiado correctamente.';
            } catch (error) {
                message.textContent =
                    'Selecciona el código y cópialo manualmente.';
            }
        });
    });
</script>
@endif
@endsection