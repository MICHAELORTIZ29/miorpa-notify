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
    </section>
@endsection