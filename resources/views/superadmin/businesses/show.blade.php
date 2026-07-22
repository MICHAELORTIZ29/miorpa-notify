@extends('superadmin.layout')

@section('title', $business->name . ' | MIORPA NOTIFY')

@push('styles')
    <style>
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-card {
            padding: 24px;
        }

        .detail-card h2 {
            margin-top: 0;
        }

        .detail-list {
            display: grid;
            gap: 15px;
        }

        .detail-item small {
            display: block;
            margin-bottom: 4px;
            color: var(--muted);
        }

        .user-row {
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }

        .user-row:last-child {
            border-bottom: 0;
        }

        .user-information {
            display: grid;
            gap: 5px;
        }

        .user-meta {
            color: var(--muted);
        }

        .status-active {
            color: #08783e;
            font-weight: 700;
        }

        .status-inactive {
            color: #b42318;
            font-weight: 700;
        }

        @media (max-width: 760px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
        .heading-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.heading-actions form {
    margin: 0;
}

.button-danger {
    border: 0;
    background: #b42318;
    color: white;
    cursor: pointer;
}

.button-success {
    border: 0;
    background: #08783e;
    color: white;
    cursor: pointer;
}
.subscription-card {
    grid-column: 1 / -1;
}

.subscription-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
}

.subscription-header h2 {
    margin-bottom: 5px;
}

.subscription-header p {
    margin: 0;
    color: var(--muted);
}

.subscription-status {
    display: inline-flex;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
}

.subscription-status-active {
    color: #08783e;
    background: #e9f9ef;
}

.subscription-status-trial {
    color: #155e75;
    background: #e6f7fb;
}

.subscription-status-overdue {
    color: #92400e;
    background: #fff4df;
}

.subscription-status-suspended {
    color: #b42318;
    background: #feeceb;
}

.subscription-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.subscription-stat {
    padding: 18px;
    border: 1px solid var(--border);
    border-radius: 13px;
    background: #f8fafc;
}

.subscription-stat small {
    display: block;
    margin-bottom: 7px;
    color: var(--muted);
}

.subscription-stat strong {
    display: block;
    font-size: 17px;
}

.subscription-limits-title {
    margin: 28px 0 14px;
}

.subscription-limits {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.limit-card {
    padding: 20px;
    border: 1px solid var(--border);
    border-radius: 13px;
}

.limit-card small {
    display: block;
    margin-bottom: 8px;
    color: var(--muted);
}

.limit-card strong {
    font-size: 27px;
}

.subscription-warning {
    padding: 14px 16px;
    margin-top: 20px;
    color: #92400e;
    background: #fff4df;
    border: 1px solid #f5d598;
    border-radius: 11px;
}

@media (max-width: 900px) {
    .subscription-summary {
        grid-template-columns: 1fr 1fr;
    }

    .subscription-limits {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 560px) {
    .subscription-header {
        flex-direction: column;
    }

    .subscription-summary {
        grid-template-columns: 1fr;
    }
}
        
    </style>
@endpush

@section('superadmin-content')
    <div class="page-heading">
        <div>
            <h1>{{ $business->name }}</h1>
            <p>Detalle general del negocio.</p>
        </div>

        <div class="heading-actions">
    <a
        class="button button-secondary button-link"
        href="{{ route('superadmin.businesses.index') }}"
    >
        Volver
    </a>

    <a
        class="button button-link"
        href="{{ route('superadmin.businesses.edit', $business) }}"
    >
        Editar
    </a>

    @if ($business->status === \App\Models\Business::STATUS_SUSPENDED)
        <form
            method="POST"
            action="{{ route('superadmin.businesses.activate', $business) }}"
        >
            @csrf
            @method('PATCH')

            <button
                class="button button-success"
                type="submit"
                onclick="return confirm('¿Deseas activar este negocio?')"
            >
                Activar
            </button>
        </form>
    @else
        <form
            method="POST"
            action="{{ route('superadmin.businesses.suspend', $business) }}"
        >
            @csrf
            @method('PATCH')

            <button
                class="button button-danger"
                type="submit"
                onclick="return confirm('¿Deseas suspender este negocio? Sus usuarios ya no podrán ingresar.')"
            >
                Suspender
            </button>
        </form>
    @endif
</div>

    </div>

    <section class="detail-grid">
        <article class="panel detail-card">
            <h2>Información</h2>

            <div class="detail-list">
                <div class="detail-item">
                    <small>Nombre comercial</small>
                    <strong>{{ $business->name }}</strong>
                </div>

                <div class="detail-item">
                    <small>Razón social</small>
                    <strong>{{ $business->legal_name ?: 'No registrada' }}</strong>
                </div>

                <div class="detail-item">
                    <small>RUC</small>
                    <strong>{{ $business->tax_id ?: 'No registrado' }}</strong>
                </div>

                <div class="detail-item">
                    <small>Teléfono</small>
                    <strong>{{ $business->contact_phone ?: 'No registrado' }}</strong>
                </div>

                <div class="detail-item">
                    <small>Estado</small>
                    <strong>
                        @switch($business->status)
                            @case('active')
                                Activo
                                @break

                            @case('trial')
                                En prueba
                                @break

                            @case('suspended')
                                Suspendido
                                @break

                            @case('closed')
                                Cerrado
                                @break

                            @default
                                {{ ucfirst($business->status) }}
                        @endswitch
                    </strong>
                </div>
            </div>
        </article>

        <article class="panel detail-card">
            <h2>Usuarios</h2>

            @forelse ($business->users as $user)
                <div class="user-row">
                    <div class="user-information">
                        <strong>{{ $user->name }}</strong>

                        <span class="user-meta">
                            {{ $user->email }}
                        </span>

                        <span class="user-meta">
                            Rol:
                            {{ $user->role_code === 'administrator' ? 'Administrador' : 'Cajero' }}
                        </span>

                        <span class="{{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ $user->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            @empty
                <p>Este negocio todavía no tiene usuarios registrados.</p>
            @endforelse
        </article>
        <article class="panel detail-card subscription-card">
    @php
        $subscription = $business->currentSubscription;
    @endphp

    <div class="subscription-header">
        <div>
            <h2>Plan y suscripción</h2>

            <p>
                Condiciones comerciales y restricciones del cliente.
            </p>
        </div>

        @if ($subscription)
            <span
                class="
                    subscription-status
                    subscription-status-{{ $subscription->status }}
                "
            >
                @switch($subscription->status)
                    @case('active')
                        Activa
                        @break

                    @case('trial')
                        En prueba
                        @break

                    @case('overdue')
                        Pago vencido
                        @break

                    @case('suspended')
                        Suspendida
                        @break

                    @case('cancelled')
                        Cancelada
                        @break

                    @default
                        {{ ucfirst($subscription->status) }}
                @endswitch
            </span>
        @endif
    </div>

    @if ($subscription)
        <div class="subscription-summary">
            <div class="subscription-stat">
                <small>Plan contratado</small>

                <strong>
                    {{ $subscription->plan->name }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Ciclo de pago</small>

                <strong>
                    {{ $subscription->billing_cycle === 'annual'
                        ? 'Anual'
                        : 'Mensual' }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Precio</small>

                <strong>
                    @if ($subscription->price !== null)
                        S/
                        {{ number_format(
                            (float) $subscription->price,
                            2
                        ) }}
                    @else
                        No definido
                    @endif
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Suspensión automática</small>

                <strong>
                    {{ $subscription->auto_suspend
                        ? 'Activada'
                        : 'Desactivada' }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Fecha de inicio</small>

                <strong>
                    {{ $subscription->starts_at
                        ->timezone('America/Lima')
                        ->format('d/m/Y') }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Próxima fecha de pago</small>

                <strong>
                    {{ $subscription->current_period_ends_at
                        ->timezone('America/Lima')
                        ->format('d/m/Y') }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Fin del periodo de gracia</small>

                <strong>
                    {{ $subscription->grace_ends_at
                        ?->timezone('America/Lima')
                        ->format('d/m/Y')
                        ?? 'Sin gracia' }}
                </strong>
            </div>

            <div class="subscription-stat">
                <small>Moneda</small>

                <strong>
                    {{ $subscription->currency }}
                </strong>
            </div>
        </div>

        <h3 class="subscription-limits-title">
            Restricciones del cliente
        </h3>

        <div class="subscription-limits">
            <div class="limit-card">
                <small>Dispositivos emisores</small>

                <strong>
                    {{ $subscription->limit(
                        \App\Models\Subscription::LIMIT_EMITTERS
                    ) ?? 0 }}
                </strong>

                <span>máximo</span>
            </div>

            <div class="limit-card">
                <small>Dispositivos receptores</small>

                <strong>
                    {{ $subscription->limit(
                        \App\Models\Subscription::LIMIT_RECEIVERS
                    ) ?? 0 }}
                </strong>

                <span>máximo</span>
            </div>

            <div class="limit-card">
                <small>Cajeros</small>

                <strong>
                    {{ $subscription->limit(
                        \App\Models\Subscription::LIMIT_CASHIERS
                    ) ?? 0 }}
                </strong>

                <span>máximo</span>
            </div>
        </div>

        @if ($subscription->warning())
            <div class="subscription-warning">
                {{ $subscription->warning()['message'] }}
            </div>
        @endif
    @else
        <p>
            Este negocio todavía no tiene una suscripción configurada.
        </p>
    @endif
</article>
    </section>
    
@endsection