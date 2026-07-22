@extends('business.layout')

@section('title', 'Detalle del pago | MIORPA NOTIFY')

@push('styles')
<style>
    .payment-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }

    .detail-card {
        padding: 25px;
    }

    .detail-card h2 {
        margin-top: 0;
    }

    .payment-amount {
        margin: 15px 0 25px;
        color: #08783e;
        font-size: 48px;
    }

    .detail-list {
        display: grid;
        gap: 16px;
    }

    .detail-item small {
        display: block;
        margin-bottom: 4px;
        color: var(--muted);
    }

    .acknowledgement {
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }

    .acknowledgement:last-child {
        border-bottom: 0;
    }

    .confirmation-form {
        margin-top: 24px;
    }

    @media (max-width: 750px) {
        .payment-detail {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('business-content')
@php
    $myAcknowledgement = $payment
        ->acknowledgements
        ->firstWhere(
            'user_id',
            auth()->id()
        );

    $confirmation = $payment
        ->acknowledgements
        ->first(
            fn ($acknowledgement) =>
                $acknowledgement
                    ->confirmed_at !== null
        );
@endphp

<div class="page-heading">
    <div>
        <h1>Detalle del pago</h1>
        <p>{{ $payment->provider->name }}</p>
    </div>

    <a
        class="button button-secondary button-link"
        href="{{ route('business.payments.index') }}"
    >
        Volver
    </a>
</div>

<section class="payment-detail">
    <article class="panel detail-card">
        <h2>Pago recibido</h2>

        <div class="payment-amount">
            S/ {{ number_format($payment->amount, 2) }}
        </div>

        <div class="detail-list">
            <div class="detail-item">
                <small>Cliente</small>
                <strong>
                    {{ $payment->payer_name ?: 'No identificado' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Fecha y hora</small>
                <strong>
                    {{ $payment->occurred_at
                        ->timezone('America/Lima')
                        ->format('d/m/Y H:i:s') }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Medio de pago</small>
                <strong>{{ $payment->provider->name }}</strong>
            </div>

            <div class="detail-item">
                <small>Referencia</small>
                <strong>
                    {{ $payment->external_reference ?: 'No disponible' }}
                </strong>
            </div>

            <div class="detail-item">
                <small>Dispositivo emisor</small>
                <strong>{{ $payment->emitterDevice->name }}</strong>
            </div>
        </div>

        @if (! $confirmation)
    <form
        class="confirmation-form"
        method="POST"
        action="{{ route(
            'business.payments.confirm',
            $payment
        ) }}"
    >
        @csrf
        @method('PATCH')

        <button
            class="button"
            type="submit"
        >
            Marcar como verificado
        </button>
    </form>
@elseif (
    $confirmation->user_id ===
    auth()->id()
)
    <div
        class="alert alert-success confirmation-form"
    >
        Confirmaste este pago el

        {{ $confirmation
            ->confirmed_at
            ->timezone('America/Lima')
            ->format('d/m/Y H:i:s') }}.
    </div>
@else
    <div
        class="alert alert-warning confirmation-form"
    >
        Este pago ya fue verificado por

        <strong>
            {{ $confirmation->user->name }}
        </strong>

        el

        {{ $confirmation
            ->confirmed_at
            ->timezone('America/Lima')
            ->format('d/m/Y H:i:s') }}.
    </div>
@endif
    </article>

    <article class="panel detail-card">
        <h2>Historial de verificación</h2>

        @forelse ($payment->acknowledgements as $acknowledgement)
            <div class="acknowledgement">
                <strong>{{ $acknowledgement->user->name }}</strong>

                <div>
                    Visto:
                    {{ $acknowledgement->viewed_at
                        ? $acknowledgement->viewed_at
                            ->timezone('America/Lima')
                            ->format('d/m/Y H:i:s')
                        : 'No' }}
                </div>
                <div>
    Dispositivo:
    <strong>
        {{ $acknowledgement->receiverDevice?->name
            ?? 'No identificado' }}
    </strong>
</div>

<div>
    Tipo de usuario:
    <strong>
        @switch($acknowledgement->user->role_code)
            @case(\App\Models\User::ROLE_ADMINISTRATOR)
                Administrador
                @break

            @case(\App\Models\User::ROLE_CASHIER)
                Cajero
                @break

            @default
                Usuario
        @endswitch
    </strong>
</div>

                <div>
                    Confirmado:
                    {{ $acknowledgement->confirmed_at
                        ? $acknowledgement->confirmed_at
                            ->timezone('America/Lima')
                            ->format('d/m/Y H:i:s')
                        : 'No' }}
                </div>
            </div>
        @empty
            <p>Ningún usuario ha revisado este pago.</p>
        @endforelse
    </article>
</section>
@endsection