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
        grid-template-columns:
            2fr repeat(4, 1fr) auto;
        gap: 12px;
        padding: 20px;
        margin-bottom: 22px;
    }

    .filters input,
    .filters select {
        min-width: 0;
        padding: 11px;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: white;
        font: inherit;
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
        .filters {
            grid-template-columns: 1fr 1fr;
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

    .payment-summary,
    .filters {
        grid-template-columns: 1fr;
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
        <span>Pagos recibidos hoy</span>

        <strong>
            {{ $todayPaymentCount }}
        </strong>
    </article>

    <article class="panel summary-card">
        <span>Total recibido hoy</span>

        <strong>
            S/ {{ number_format(
                $todayPaymentTotal,
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
    <input
        name="search"
        type="search"
        placeholder="Buscar cliente o referencia"
        value="{{ $filters['search'] ?? '' }}"
        aria-label="Buscar cliente o referencia"
    >

    <select
        name="provider"
        aria-label="Medio de pago"
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

    <select
        name="status"
        aria-label="Estado del pago"
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

    <input
        name="date_from"
        type="date"
        value="{{ $filters['date_from'] ?? '' }}"
        title="Fecha desde"
        aria-label="Fecha desde"
    >

    <input
        name="date_to"
        type="date"
        value="{{ $filters['date_to'] ?? '' }}"
        title="Fecha hasta"
        aria-label="Fecha hasta"
    >

    <button
        class="button"
        type="submit"
    >
        Buscar
    </button>
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

        if (alertsEnabled) {
            await prepareAudio();

            if (
                'Notification' in window &&
                Notification.permission === 'default'
            ) {
                await Notification.requestPermission();
            }

            playPaymentSound();
        }

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