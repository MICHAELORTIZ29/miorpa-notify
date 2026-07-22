@extends('business.layout')

@section('title', 'Vincular dispositivo | MIORPA NOTIFY')

@push('styles')
    <style>
        .receiver-link-container {
            width: min(620px, 100%);
            margin: 20px auto;
        }

        .receiver-link-card {
            padding: 30px;
        }

        .receiver-link-card h1 {
            margin-top: 0;
        }

        .receiver-link-description {
            margin-bottom: 25px;
            color: var(--muted);
            line-height: 1.6;
        }

        .receiver-link-form {
            display: grid;
            gap: 20px;
        }

        .receiver-link-field {
            display: grid;
            gap: 8px;
        }

        .receiver-link-field label {
            font-weight: 700;
        }

        .receiver-link-field input {
            box-sizing: border-box;
            width: 100%;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font: inherit;
        }

        .receiver-code {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
        }

        .receiver-help {
            padding: 16px;
            color: #155e75;
            background: #e6f7fb;
            border-radius: 11px;
            line-height: 1.5;
        }

        .field-error {
            color: #b42318;
            font-size: 14px;
        }
    </style>
@endpush

@section('business-content')
    <div class="receiver-link-container">
        <section class="panel receiver-link-card">
            <h1>Vincular este dispositivo</h1>

            <p class="receiver-link-description">
                Ingresa el código de receptor generado por el
                administrador de {{ auth()->user()->business->name }}.
                Este navegador ocupará un cupo de dispositivos receptores.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form
                class="receiver-link-form"
                method="POST"
                action="{{ route('receiver.link.store') }}"
                id="receiver-link-form"
            >
                @csrf

                <input
                    id="installation_id"
                    name="installation_id"
                    type="hidden"
                >

                <div class="receiver-link-field">
                    <label for="code">
                        Código de receptor
                    </label>

                    <input
                        id="code"
                        class="receiver-code"
                        name="code"
                        type="text"
                        maxlength="13"
                        placeholder="MNP-ABCD-EFGH"
                        value="{{ old('code') }}"
                        autocomplete="off"
                        required
                    >

                    @error('code')
                        <span class="field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="receiver-link-field">
                    <label for="device_name">
                        Nombre de este dispositivo
                    </label>

                    <input
                        id="device_name"
                        name="device_name"
                        type="text"
                        maxlength="120"
                        value="{{ old(
                            'device_name',
                            'Navegador de ' . auth()->user()->name
                        ) }}"
                        required
                    >

                    @error('device_name')
                        <span class="field-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="receiver-help">
                    El código es de un solo uso. Si este dispositivo
                    es revocado, será necesario generar uno nuevo.
                </div>

                <button
                    class="button"
                    type="submit"
                >
                    Vincular dispositivo
                </button>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                const storageKey =
                    'miorpa_receiver_installation_id';

                let installationId =
                    window.localStorage.getItem(storageKey);

                if (!installationId) {
                    if (
                        window.crypto &&
                        typeof window.crypto.randomUUID ===
                            'function'
                    ) {
                        installationId =
                            window.crypto.randomUUID();
                    } else {
                        installationId =
                            'web-' +
                            Date.now() +
                            '-' +
                            Math.random()
                                .toString(36)
                                .slice(2) +
                            '-' +
                            Math.random()
                                .toString(36)
                                .slice(2);
                    }

                    window.localStorage.setItem(
                        storageKey,
                        installationId
                    );
                }

                document.getElementById(
                    'installation_id'
                ).value = installationId;

                const codeInput =
                    document.getElementById('code');

                codeInput.addEventListener(
                    'input',
                    function () {
                        this.value =
                            this.value.toUpperCase();
                    }
                );
            }
        );
    </script>
@endsection