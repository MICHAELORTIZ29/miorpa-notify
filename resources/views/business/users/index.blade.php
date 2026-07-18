@extends('business.layout')

@section('title', 'Cajeros | MIORPA NOTIFY')

@push('styles')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 28px;
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

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table th,
    .users-table td {
        padding: 16px 20px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .table-actions form {
        margin: 0;
    }

    .status-active {
        color: #08783e;
        font-weight: 700;
    }

    .status-disabled {
        color: #b42318;
        font-weight: 700;
    }

    .empty-state {
        padding: 40px;
        text-align: center;
        color: var(--muted);
    }

    @media (max-width: 700px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('business-content')
<div class="page-heading">
    <div>
        <h1>Cajeros</h1>
        <p>Administra los accesos de los usuarios de tu negocio.</p>
    </div>

    <a
        class="button button-link"
        href="{{ route('business.users.create') }}"
    >
        Crear cajero
    </a>
</div>

<section class="summary-grid">
    <article class="panel summary-card">
        <span>Cajeros activos</span>
        <strong>{{ $activeUsers }}</strong>
    </article>

    <article class="panel summary-card">
        <span>Cajeros inactivos</span>
        <strong>{{ $inactiveUsers }}</strong>
    </article>
</section>

<section class="panel table-container">
    @if ($users->isEmpty())
        <div class="empty-state">
            Todavía no tienes cajeros registrados.
        </div>
    @else
        <table class="users-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="{{ $user->status === 'active'
                                ? 'status-active'
                                : 'status-disabled' }}"
                            >
                                {{ $user->status === 'active'
                                    ? 'Activo'
                                    : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a
                                    class="button button-secondary button-link"
                                    href="{{ route('business.users.edit', $user) }}"
                                >
                                    Editar
                                </a>

                                @if ($user->status === 'active')
                                    <form
                                        method="POST"
                                        action="{{ route('business.users.deactivate', $user) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="button button-secondary"
                                            type="submit"
                                            onclick="return confirm('¿Deseas desactivar este cajero?')"
                                        >
                                            Desactivar
                                        </button>
                                    </form>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('business.users.activate', $user) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="button" type="submit">
                                            Activar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    @endif
</section>
@endsection