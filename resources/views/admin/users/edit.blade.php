@extends('admin.layout')

@section('title', 'Editar usuario')

@section('content')
    <div class="d-flex align-items-center gap-3 mb-4">
        <a class="text-muted text-decoration-none" href="{{ route('admin.users') }}">← Usuarios</a>
        <h2 class="mb-0">Editar usuario</h2>
    </div>

    <div class="card shadow-sm p-4" style="max-width: 560px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nueva contraseña <span class="text-muted small">(dejar en blanco para no cambiar)</span></label>
                <input id="password" name="password" type="password"
                       class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="form-control" autocomplete="new-password">
            </div>

            <div class="mb-4 form-check">
                <input id="is_admin" name="is_admin" type="checkbox" class="form-check-input" value="1"
                       {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                <label for="is_admin" class="form-check-label">Administrador</label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-semibold">Guardar cambios</button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
