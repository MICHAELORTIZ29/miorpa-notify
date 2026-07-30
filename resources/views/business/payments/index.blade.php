@extends('business.layout')

@section('title', 'Pagos | MIORPA NOTIFY')

@push('styles')
<style>
    .payment-page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .live-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .notification-button {
        padding: 9px 13px;
        color: #155e75;
        background: #e6f7fb;
        border: 1px solid #bae6ef;
        border-radius: 999px;
        cursor: pointer;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
    }

    .notification-button.enabled {
        color: #08783e;
        background: #e9f9ef;
        border-color: #bcebd0;
    }

    .notification-button.blocked {
        color: #92400e;
        background: #fff4df;
        border-color: #f4d49d;
    }

    .payment-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
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
        font-size: 34px;
    }

    .live-indicator {
        display: inline-flex;
        align-items: center;
        flex-shrink: 0;
        gap: 8px;
        padding: 9px 13px;
        color: #08783e;
        background: #e9f9ef;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .live-indicator::before {
        width: 9px;
        height: 9px;
        content: "";
        background: #16a365;
        border-radius: 50%;
        box-shadow:
            0 0 0 4px rgba(22, 163, 101, .12);
    }

    .live-indicator.checking {
        color: #155e75;
        background: #e6f7fb;
    }

    .live-indicator.checking::before {
        background: #0891b2;
    }

    .live-indicator.offline {
        color: #92400e;
        background: #fff4df;
    }

    .live-indicator.offline::before {
        background: #d97706;
    }

    .live-indicator.new-payment {
        color: white;
        background: #08783e;
    }

    .live-indicator.new-payment::before {
        background: white;
    }

    .filters {
        display: grid;
        gap: 18px;
        padding: 20px;
        margin-bottom: 22px;
    }

    .filters-primary {
        display: grid;
        grid-template-columns: minmax(260px, 2fr) 1fr 1fr;
        gap: 12px;
    }

    .filters-period {
        display: grid;
        grid-template-columns: repeat(4, minmax(145px, 1fr)) auto;
        align-items: end;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    .filter-field {
        display: grid;
        min-width: 0;
        gap: 7px;
    }

    .filter-field label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .filters input,
    .filters select {
        width: 100%;
        min-width: 0;
        min-height: 46px;
        box-sizing: border-box;
        padding: 11px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: white;
        font: inherit;
    }

    .filters input:focus,
    .filters select:focus {
        outline: 3px solid rgba(8, 145, 178, .12);
        border-color: #0891b2;
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .filter-actions .button,
    .filter-clear {
        min-height: 46px;
        white-space: nowrap;
    }

    .filter-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        color: var(--primary-dark);
        text-decoration: none;
        background: #f2f6fa;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
    }

    .filter-clear:hover {
        background: #e7eef5;
    }

    .filter-clear-hours {
        color: #92400e;
        background: #fff8e8;
        border-color: #f2d7a5;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .payments-table {
        width: 100%;
        border-collapse: collapse;
    }

    .payments-table th,
    .payments-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .payments-table tbody tr {
        transition: background-color .2s ease;
    }

    .payments-table tbody tr:hover {
        background: #f8fbfd;
    }

    .amount {
        font-size: 18px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-received {
        color: #92400e;
        background: #fff4df;
    }

    .status-confirmed {
        color: #08783e;
        background: #e9f9ef;
    }

    .status-ignored {
        color: #b42318;
        background: #feeceb;
    }

    .empty-state {
        padding: 45px;
        color: var(--muted);
        text-align: center;
    }

    .pagination-container {
        padding: 18px;
    }

    .payment-toast {
        position: fixed;
        z-index: 9999;
        right: 24px;
        bottom: 24px;
        width: min(
            390px,
            calc(100% - 48px)
        );
        padding: 20px;
        color: white;
        background: #08783e;
        border-radius: 16px;
        box-shadow:
            0 18px 50px rgba(8, 120, 62, .35);
        animation:
            payment-toast-enter .25s ease-out;
    }

    .payment-toast[hidden] {
        display: none;
    }

    .payment-toast-title {
        margin-bottom: 8px;
        font-size: 19px;
        font-weight: 800;
    }

    .payment-toast-amount {
        margin-bottom: 6px;
        font-size: 31px;
        font-weight: 900;
    }

    .payment-toast-client {
        color: #d9f8e7;
    }

    @keyframes payment-toast-enter {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 1000px) {
        .filters-primary {
            grid-template-columns: 1fr 1fr;
        }

        .filters-primary .filter-search {
            grid-column: 1 / -1;
        }

        .filters-period {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }
@media (max-width: 680px) {
    .payment-page-heading {
        align-items: stretch;
        flex-direction: column;
    }

    .live-controls {
        align-items: stretch;
        flex-direction: column;
    }

    .live-controls > * {
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

    .payment-summary {
        grid-template-columns: 1fr;
    }

    .filters-primary,
    .filters-period {
        grid-template-columns: 1fr;
    }

    .filters-primary .filter-search,
    .filter-actions {
        grid-column: auto;
    }

    .filter-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .filter-actions .button,
    .filter-clear {
        width: 100%;
        box-sizing: border-box;
    }

    .summary-card strong {
        font-size: 29px;
    }

    .table-scroll {
        overflow: visible;
    }

    .payments-table {
        display: block;
        min-width: 0;
        padding: 12px;
    }

    .payments-table thead {
        display: none;
    }

    .payments-table tbody {
        display: grid;
        gap: 14px;
    }

    .payments-table tr {
        display: block;
        overflow: hidden;
        background: white;
        border: 1px solid var(--border);
        border-radius: 15px;
        box-shadow:
            0 7px 20px rgba(18, 58, 99, .07);
    }

    .payments-table tbody tr:hover {
        background: white;
    }

    .payments-table td {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 11px 14px;
        text-align: right;
        border-bottom: 1px solid var(--border);
    }

    .payments-table td:last-child {
        border-bottom: 0;
    }

    .payments-table td::before {
        flex-shrink: 0;
        content: attr(data-label);
        color: var(--muted);
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .payments-table td[data-label="Cliente"] {
        display: block;
        padding: 16px 14px 12px;
        color: var(--primary-dark);
        text-align: left;
        font-size: 16px;
    }

    .payments-table td[data-label="Cliente"]::before {
        display: block;
        margin-bottom: 7px;
    }

    .payments-table td[data-label="Monto"] {
        align-items: center;
        color: var(--primary-dark);
        background: #f7fbff;
    }

    .payments-table td[data-label="Monto"] .amount,
    .payments-table td.amount {
        font-size: 22px;
    }

    .payments-table td[data-label="Acciones"] {
        display: block;
        padding: 13px;
    }

    .payments-table td[data-label="Acciones"]::before {
        display: none;
    }

    .payments-table td[data-label="Acciones"] .button-link {
        width: 100%;
        min-height: 44px;
        box-sizing: border-box;
    }

    .payment-toast {
        right: 12px;
        bottom: 90px;
        width: calc(100% - 24px);
        box-sizing: border-box;
    }

    .empty-state {
        padding: 35px 18px;
    }
}
    .payments-pagination {
    margin-top: 22px;
}

.payments-pagination nav {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.payments-pagination nav > div:first-child {
    display: none;
}

.payments-pagination nav > div:last-child {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.payments-pagination nav > div:last-child > div:first-child {
    color: var(--muted);
    font-size: 14px;
}

.payments-pagination nav > div:last-child > div:last-child {
    display: flex;
    align-items: center;
}

.payments-pagination span[aria-current="page"] > span {
    color: white;
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

.payments-pagination a,
.payments-pagination span {
    box-sizing: border-box;
}

.payments-pagination a,
.payments-pagination span > span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 12px;
    color: var(--primary-dark);
    text-decoration: none;
    background: white;
    border: 1px solid var(--border);
}

.payments-pagination a:hover {
    color: white;
    background: var(--primary);
    border-color: var(--primary);
}

.payments-pagination svg {
    display: block !important;
    width: 18px !important;
    height: 18px !important;
    max-width: 18px !important;
    max-height: 18px !important;
}

@media (max-width: 620px) {
    .payments-pagination nav > div:last-child {
        align-items: stretch;
        flex-direction: column;
    }

    .payments-pagination nav > div:last-child > div:last-child {
        overflow-x: auto;
        padding-bottom: 4px;
    }
}
</style>
@endpush

@section('business-content')
<div class="page-heading payment-page-heading">
    <div>
        <h1>Pagos</h1>

        <p>
            Consulta y confirma los pagos recibidos.
        </p>
    </div>
    

    <div class="live-controls">
        @if (auth()->user()->isAdministrator())
    <a
        class="notification-button"
        style="text-decoration: none;"
        href="{{ route(
            'business.payments.export',
            request()->query()
        ) }}"
    >
        Exportar CSV
    </a>
@endif
        <button
            id="notification-button"
            class="notification-button"
            type="button"
        >
            Activar sonido y avisos
        </button>

        <div
            id="live-indicator"
            class="live-indicator checking"
            role="status"
            aria-live="polite"
        >
            Comprobando conexión
        </div>
    </div>
</div>

<div
    id="payment-toast"
    class="payment-toast"
    role="alert"
    aria-live="assertive"
    hidden
>
    <div class="payment-toast-title">
        Nuevo pago recibido
    </div>

    <div
        id="payment-toast-amount"
        class="payment-toast-amount"
    ></div>

    <div
        id="payment-toast-client"
        class="payment-toast-client"
    ></div>
</div>

<section
    id="payment-summary"
    class="payment-summary"
>
    <article class="panel summary-card">
        <span>
            {{ $hasActiveFilters
                ? 'Pagos encontrados'
                : 'Pagos recibidos hoy' }}
        </span>

        <strong>
            {{ $hasActiveFilters
                ? $filteredPaymentCount
                : $todayPaymentCount }}
        </strong>
    </article>

    <article class="panel summary-card">
        <span>
            {{ $hasActiveFilters
                ? 'Total filtrado'
                : 'Total recibido hoy' }}
        </span>

        <strong>
            S/
            {{ number_format(
                $hasActiveFilters
                    ? $filteredPaymentTotal
                    : $todayPaymentTotal,
                2
            ) }}
        </strong>
    </article>
</section>

<form
    class="panel filters"
    method="GET"
    action="{{ route(
        'business.payments.index'
    ) }}"
>
    <div class="filters-primary">
        <div class="filter-field filter-search">
            <label for="filter-search">
                Cliente o referencia
            </label>

            <input
                id="filter-search"
                name="search"
                type="search"
                placeholder="Buscar cliente o referencia"
                value="{{ $filters['search'] ?? '' }}"
            >
        </div>

        <div class="filter-field">
            <label for="filter-provider">
                Medio de pago
            </label>

            <select
                id="filter-provider"
                name="provider"
            >
                <option value="">
                    Todos los medios
                </option>

                @foreach ($providers as $provider)
                    <option
                        value="{{ $provider->code }}"
                        @selected(
                            ($filters['provider'] ?? '') ===
                            $provider->code
                        )
                    >
                        {{ $provider->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="filter-status">
                Estado
            </label>

            <select
                id="filter-status"
                name="status"
            >
                <option value="">
                    Todos los estados
                </option>

                <option
                    value="received"
                    @selected(
                        ($filters['status'] ?? '') ===
                        'received'
                    )
                >
                    Recibido
                </option>

                <option
                    value="confirmed"
                    @selected(
                        ($filters['status'] ?? '') ===
                        'confirmed'
                    )
                >
                    Verificado
                </option>
            </select>
        </div>
    </div>

    <div class="filters-period">
        <div class="filter-field">
            <label for="filter-date-from">
                Fecha desde
            </label>

            <input
                id="filter-date-from"
                name="date_from"
                type="date"
                value="{{ $filters['date_from'] ?? '' }}"
            >
        </div>

        <div class="filter-field">
            <label for="filter-date-to">
                Fecha hasta
            </label>

            <input
                id="filter-date-to"
                name="date_to"
                type="date"
                value="{{ $filters['date_to'] ?? '' }}"
            >
        </div>

        <div class="filter-field">
            <label for="filter-time-from">
                Hora desde
            </label>

            <input
                id="filter-time-from"
                name="time_from"
                type="time"
                value="{{ $filters['time_from'] ?? '' }}"
            >
        </div>

        <div class="filter-field">
            <label for="filter-time-to">
                Hora hasta
            </label>

            <input
                id="filter-time-to"
                name="time_to"
                type="time"
                value="{{ $filters['time_to'] ?? '' }}"
            >
        </div>

        <div class="filter-actions">
            <button
                class="button"
                type="submit"
            >
                Buscar
            </button>

            @if (
                filled($filters['time_from'] ?? null) ||
                filled($filters['time_to'] ?? null)
            )
                <a
                    class="filter-clear filter-clear-hours"
                    href="{{ route(
                        'business.payments.index',
                        request()->except([
                            'time_from',
                            'time_to',
                            'page',
                        ])
                    ) }}"
                >
                    Quitar horas
                </a>
            @endif

            @if (request()->query())
                <a
                    class="filter-clear"
                    href="{{ route(
                        'business.payments.index'
                    ) }}"
                >
                    Limpiar todo
                </a>
            @endif
        </div>
    </div>
</form>

<section
    id="payments-panel"
    class="panel"
>
    <div class="table-scroll">
        @if ($payments->isEmpty())
            <div class="empty-state">
                No se encontraron pagos.
            </div>
        @else
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Medio</th>
                        <th>Monto</th>
<th>Verificado por</th>
<th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td data-label="Hora">
                                {{ $payment->occurred_at
                                    ->timezone(
                                        'America/Lima'
                                    )
                                    ->format(
                                        'd/m/Y H:i:s'
                                    ) }}
                            </td>

                            <td data-label="Cliente">
                                <strong>
                                    {{ $payment->payer_name
                                        ?: 'No identificado' }}
                                </strong>

                                @if (
                                    $payment
                                        ->external_reference
                                )
                                    <br>

                                    <small>
                                        {{ $payment
                                            ->external_reference }}
                                    </small>
                                @endif
                            </td>

                            <td data-label="Medio">
                                {{ $payment
                                    ->provider
                                    ->name }}
                            </td>

                            <td class="amount"data-label="Monto">
                                S/
                                {{ number_format(
                                    $payment->amount,
                                    2
                                ) }}
                            </td>
                            @php
    $confirmation = $payment
        ->acknowledgements
        ->first();
@endphp

<td data-label="Verificado por">
    @if ($confirmation)
        <strong>
            {{ $confirmation->user?->name
                ?? 'Usuario eliminado' }}
        </strong>

        <br>

        <small>
            {{ $confirmation
                ->receiverDevice
                ?->name
                ?? 'Dispositivo no identificado' }}
        </small>
    @else
        <span style="color: var(--muted);">
    Aún no verificado
</span>
    @endif
</td>

                            <td data-label="Estado">
                                <span
                                    class="status status-{{ $payment->status }}"
                                >
                                    @switch(
                                        $payment->status
                                    )
                                        @case('confirmed')
                                            Verificado
                                            @break

                                        @case('ignored')
                                            Ignorado
                                            @break

                                        @default
                                            Recibido
                                    @endswitch
                                </span>
                            </td>

                            <td data-label="Acciones">
                                <a
                                    class="button button-secondary button-link"
                                    href="{{ route(
                                        'business.payments.show',
                                        $payment
                                    ) }}"
                                >
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($payments->hasPages())
        <div class="payments-pagination">
    {{ $payments->onEachSide(1)->links() }}
</div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const liveIndicator = document.getElementById('live-indicator');
    const notificationButton = document.getElementById('notification-button');
    const paymentToast = document.getElementById('payment-toast');
    const paymentToastAmount = document.getElementById('payment-toast-amount');
    const paymentToastClient = document.getElementById('payment-toast-client');

    let latestPaymentPublicId = @json($latestPaymentPublicId);
    let requestInProgress = false;
    let audioContext = null;
    let soundReady = false;
    let toastTimer = null;
    let notificationRegistration = null;

    function base64ToUint8Array(base64String) {
    const padding = '='.repeat(
        (4 - base64String.length % 4) % 4
    );

    const base64 =
        (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

    const rawData = window.atob(base64);

    return Uint8Array.from(
        [...rawData].map(function (char) {
            return char.charCodeAt(0);
        })
    );
}

async function registerPushSubscription() {
    if (
        !('serviceWorker' in navigator) ||
        !('PushManager' in window) ||
        !('Notification' in window)
    ) {
        console.warn(
            'Este navegador no soporta Web Push'
        );

        return false;
    }

    if (Notification.permission !== 'granted') {
        console.warn(
            'El permiso de notificaciones no fue concedido'
        );

        return false;
    }

    try {
        const registration =
            await navigator.serviceWorker.ready;

        let subscription =
            await registration.pushManager
                .getSubscription();

        if (!subscription) {
            const keyResponse = await fetch(
                @json(route('push.vapid-public-key')),
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                }
            );

            if (!keyResponse.ok) {
                throw new Error(
                    'No se pudo obtener la clave pública VAPID. HTTP ' +
                    keyResponse.status
                );
            }

            const keyData =
                await keyResponse.json();

            const publicKey = String(
                keyData.publicKey || ''
            ).trim();

            if (!publicKey) {
                throw new Error(
                    'La clave pública VAPID está vacía'
                );
            }

            const applicationServerKey =
                base64ToUint8Array(publicKey);

            console.log(
                'Longitud de clave VAPID decodificada:',
                applicationServerKey.length
            );

            console.log(
                'Service Worker:',
                registration
            );

            console.log(
                'ApplicationServerKey:',
                applicationServerKey
            );

            if (
                applicationServerKey.length !== 65
            ) {
                throw new Error(
                    'La clave pública VAPID no es válida. ' +
                    'Longitud decodificada: ' +
                    applicationServerKey.length
                );
            }

            subscription =
                await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey:
                        applicationServerKey
                });
        }

        const subscriptionJson =
            subscription.toJSON();

        const response = await fetch(
            @json(route('push.subscriptions.store')),
            {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/json',

                    'Accept':
                        'application/json',

                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).getAttribute('content')
                },

                credentials: 'same-origin',

                body: JSON.stringify({
                    endpoint:
                        subscriptionJson.endpoint,

                    keys:
                        subscriptionJson.keys
                })
            }
        );

        if (!response.ok) {
            const errorText =
                await response.text();

            throw new Error(
                'No se pudo guardar la suscripción. HTTP ' +
                response.status +
                ': ' +
                errorText
            );
        }

        console.log(
            'Web Push registrado correctamente',
            subscription
        );

        return true;
    } catch (error) {
        console.error(
            'ERROR COMPLETO:',
            error
        );

        console.error(
            'Nombre:',
            error.name
        );

        console.error(
            'Mensaje:',
            error.message
        );

        console.error(
            'Stack:',
            error.stack
        );

        return false;
    }
}



    let alertsEnabled =
        localStorage.getItem('miorpa_alerts_enabled') !== '0';

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then(function (registration) {
            notificationRegistration = registration;
        });
    }

    function updateIndicator(state, message) {
        liveIndicator.classList.remove(
            'checking',
            'offline',
            'new-payment'
        );

        if (state) {
            liveIndicator.classList.add(state);
        }

        liveIndicator.textContent = message;
    }

    function updateNotificationButton() {
        notificationButton.classList.remove(
            'enabled',
            'blocked'
        );

        if (!alertsEnabled) {
            notificationButton.textContent =
                'Activar sonido y avisos';

            return;
        }

        if (
            'Notification' in window &&
            Notification.permission === 'denied'
        ) {
            notificationButton.textContent =
                'Avisos bloqueados';

            notificationButton.classList.add('blocked');

            return;
        }

        notificationButton.textContent =
            'Sonido y avisos activos';

        notificationButton.classList.add('enabled');
    }

    function createAudioContext() {
        const AudioContextClass =
            window.AudioContext ||
            window.webkitAudioContext;

        if (!AudioContextClass) {
            return null;
        }

        if (!audioContext) {
            audioContext = new AudioContextClass();
        }

        return audioContext;
    }

    async function prepareAudio() {
        const context = createAudioContext();

        if (!context) {
            return false;
        }

        if (context.state === 'suspended') {
            await context.resume();
        }

        soundReady = context.state === 'running';

        return soundReady;
    }

    function playNote(
        frequency,
        startDelay,
        duration,
        volume
    ) {
        if (
            !audioContext ||
            audioContext.state !== 'running'
        ) {
            return;
        }

        const oscillator =
            audioContext.createOscillator();

        const gain =
            audioContext.createGain();

        const startsAt =
            audioContext.currentTime +
            startDelay;

        oscillator.type = 'sine';

        oscillator.frequency.setValueAtTime(
            frequency,
            startsAt
        );

        gain.gain.setValueAtTime(
            0.0001,
            startsAt
        );

        gain.gain.exponentialRampToValueAtTime(
            volume,
            startsAt + 0.02
        );

        gain.gain.exponentialRampToValueAtTime(
            0.0001,
            startsAt + duration
        );

        oscillator.connect(gain);
        gain.connect(audioContext.destination);

        oscillator.start(startsAt);

        oscillator.stop(
            startsAt + duration + 0.03
        );
    }

    function playPaymentSound() {
        if (!alertsEnabled || !soundReady) {
            return;
        }

        playNote(659, 0, 0.20, 0.20);
        playNote(880, 0.18, 0.20, 0.20);
        playNote(1174, 0.36, 0.34, 0.24);
    }

async function toggleAlerts() {
    alertsEnabled = !alertsEnabled;

    localStorage.setItem(
        'miorpa_alerts_enabled',
        alertsEnabled ? '1' : '0'
    );

    if (!alertsEnabled) {
        updateNotificationButton();
        return;
    }

    await prepareAudio();

    if (
        'Notification' in window &&
        Notification.permission === 'default'
    ) {
        await Notification.requestPermission();
    }

    /*
     * Intentamos registrar Web Push, pero si falla,
     * no desactivamos el sonido ni los avisos de la web.
     */
    if (
        'Notification' in window &&
        Notification.permission === 'granted'
    ) {
        const pushRegistered =
            await registerPushSubscription();

        if (!pushRegistered) {
            console.warn(
                'Web Push no pudo registrarse. ' +
                'Los avisos con la página abierta seguirán activos.'
            );
        }
    }

    playPaymentSound();
    updateNotificationButton();
}

    notificationButton.addEventListener(
        'click',
        toggleAlerts
    );

    document.addEventListener(
        'pointerdown',
        function () {
            if (alertsEnabled && !soundReady) {
                prepareAudio().then(function () {
                    updateNotificationButton();
                });
            }
        },
        { once: true }
    );

    function showPaymentToast(payment) {
        if (!payment) {
            return;
        }

        paymentToastAmount.textContent =
            `S/ ${payment.amount ?? '0.00'}`;

        paymentToastClient.textContent =
            payment.payer_name ||
            'Cliente no identificado';

        paymentToast.hidden = false;

        if (toastTimer) {
            window.clearTimeout(toastTimer);
        }

        toastTimer = window.setTimeout(
            function () {
                paymentToast.hidden = true;
            },
            6000
        );
    }

 async function showBrowserNotification(payment) {
    if (
        !alertsEnabled ||
        !payment ||
        !('Notification' in window) ||
        Notification.permission !== 'granted'
    ) {
        console.warn(
            'Notificación omitida:',
            {
                alertsEnabled: alertsEnabled,
                payment: payment,
                permission:
                    'Notification' in window
                        ? Notification.permission
                        : 'no compatible'
            }
        );

        return;
    }

    const paymentId =
        payment.public_id ||
        latestPaymentPublicId ||
        Date.now().toString();

    const amount =
        payment.amount || '0.00';

    const provider =
        payment.provider || 'pago';

    const payerName =
        payment.payer_name ||
        'Cliente no identificado';

    const detailUrl =
        payment.detail_url ||
        @json(route('business.payments.index'));

    const options = {
        body: payerName,
        icon: '/logo-icon-192.png',
        badge: '/logo-icon-192.png',
        tag: `miorpa-payment-${paymentId}`,
        renotify: true,
        requireInteraction: true,
        data: {
            url: detailUrl,
            payment_id: paymentId
        }
    };

    try {
        if ('serviceWorker' in navigator) {
            const registration =
                await navigator.serviceWorker.ready;

            await registration.showNotification(
                `Nuevo ${provider}: S/ ${amount}`,
                options
            );

            console.log(
                'Notificación mostrada mediante Service Worker'
            );

            return;
        }

        const notification = new Notification(
            `Nuevo ${provider}: S/ ${amount}`,
            options
        );

        notification.onclick = function () {
            window.focus();
            window.location.href = detailUrl;
            notification.close();
        };
    } catch (error) {
        console.error(
            'No se pudo mostrar la notificación:',
            error
        );
    }
}

    async function announcePayment(payment) {
    showPaymentToast(payment);
    playPaymentSound();
    await showBrowserNotification(payment);
}

    async function refreshPaymentsContent() {
        const response = await fetch(
            window.location.href,
            {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            }
        );

        if (!response.ok) {
            throw new Error(
                'No se pudo actualizar la tabla'
            );
        }

        const html = await response.text();
        const parser = new DOMParser();
        const newDocument =
            parser.parseFromString(html, 'text/html');

        const newSummary =
            newDocument.getElementById('payment-summary');

        const newPanel =
            newDocument.getElementById('payments-panel');

        const currentSummary =
            document.getElementById('payment-summary');

        const currentPanel =
            document.getElementById('payments-panel');

        if (newSummary && currentSummary) {
            currentSummary.innerHTML =
                newSummary.innerHTML;
        }

        if (newPanel && currentPanel) {
            currentPanel.innerHTML =
                newPanel.innerHTML;
        }
    }

    async function checkForNewPayments() {
        if (requestInProgress) {
            return;
        }

        requestInProgress = true;

        try {
            const response = await fetch(
                @json(route('business.payments.live-status')),
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                }
            );

            if (
                response.status === 401 ||
                response.status === 419
            ) {
                window.location.href =
                    @json(route('login'));

                return;
            }

            if (response.status === 403) {
                window.location.href =
                    @json(route('receiver.link.create'));

                return;
            }

            if (!response.ok) {
                throw new Error(
                    'Respuesta HTTP ' +
                    response.status
                );
            }

            const data = await response.json();

            const newPaymentPublicId =
                data.latest_payment_public_id ?? null;

            if (
                newPaymentPublicId &&
                newPaymentPublicId !==
                    latestPaymentPublicId
            ) {
                latestPaymentPublicId =
                    newPaymentPublicId;

                updateIndicator(
                    'new-payment',
                    'Nuevo pago recibido'
                );

                announcePayment(
                    data.latest_payment
                );

                await refreshPaymentsContent();

                window.setTimeout(
                    function () {
                        updateIndicator(
                            '',
                            'Actualización automática'
                        );
                    },
                    4500
                );
            } else {
                latestPaymentPublicId =
                    newPaymentPublicId;

                updateIndicator(
                    '',
                    'Actualización automática'
                );
            }
        } catch (error) {
            console.error(
                'Error consultando pagos:',
                error
            );

            updateIndicator(
                'offline',
                'Intentando reconectar'
            );
        } finally {
            requestInProgress = false;
        }
    }

    updateNotificationButton();
    checkForNewPayments();

    window.setInterval(
        checkForNewPayments,
        5000
    );
});
</script>
@endsection