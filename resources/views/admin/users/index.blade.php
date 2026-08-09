@extends('admin.layout')
@section('title', 'Usuarios')

@section('content')
    <h2 class="mb-4 admin-panel-title">Usuarios</h2>
    
    <div class="d-flex align-items-center justify-content-between mb-4">
        
    </div>

    

    <div class="row">
        <div class="col-9">
            <div class="stat-card h-100">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Registrado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <span class="d-none d-md-flex align-items-center gap-3">
                                            @if($user->photo)
                                                <img src="{{ asset('storage/' . $user->photo) }}" class="border border-black d-block rounded-circle object-fit-cover" style="width: 50px; height: 50px;" alt="Foto de perfil">
                                            @else
                                                <div class="border border-black rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-warning text-dark fw-bold" style="width: 50px; height: 50px;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                            @endif
                                            <span class="d-flex flex-column">
                                                <span class="text-dark fw-bold lh-1 text-break text-start" style="font-size: 1rem;">{{ $user->name }}</span>
                                                <span class="text-secondary lh-1 text-break text-start" style="font-size: 0.8rem;">{{ $user->email }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $user->is_admin ? 'Admin' : 'Usuario' }}</td>
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge bg-success">Verificado</span>
                                        @else
                                            <span class="badge bg-secondary">No Verificado</span>
                                        @endif
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex flex-row gap-1">
                                            <a href="{{ route('admin.users.edit', $user) }}">
                                                <button  class="admin-product-card-btn bg-info">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M560-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T903-300L683-80H560Zm300-263-37-37 37 37ZM620-140h38l121-122-18-19-19-18-122 121v38ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v120h-80v-80H520v-200H240v640h240v80H240Zm280-400Zm241 199-19-18 37 37-18-19Z"/>
                                                    </svg>
                                                </button>
                                            </a>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar «{{ addslashes($user->name) }}»? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="admin-product-card-btn bg-danger">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                                </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="6">No se encontraron usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $users->withQueryString()->links() }}</div>
            </div>
        </div>

        <div class="col-3">
            <div class="stat-card p-2">
                <form class="row g-3 text-start" method="GET" action="{{ route('admin.users') }}">
                    <div class="col-sm-6 col-md-12 text-start text-white">
                        <label class="form-label" for="search">Nombre/Email</label>
                        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Buscar nombre o email...">
                    </div>
                    <div class="col-sm-6 col-md-12 text-start text-white">
                        <label class="form-label" for="is_admin">Rol</label>
                        <select class="form-select" name="is_admin">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>Administradores</option>
                            <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>Usuarios normales</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                        @if (request()->hasAny(['search', 'is_admin']))
                            <a class="btn btn-outline-secondary" href="{{ route('admin.users') }}">Limpiar</a>
                        @endif
                        <a class="btn btn-warning fw-semibold" href="{{ route('admin.users.create') }}">+</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
                                      