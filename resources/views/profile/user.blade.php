<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    </head>

    <body>
        <!-- HEADER MINIMALISTA PARA USER -->
        <header class="navbar navbar-expand-lg navbar-dark bg-black header-tall user-account-header">
            <div class="container-fluid user-account-header-bar">
                <div class="user-account-brand">
                    <img src="{{ asset('images/jp_logo.jpg') }}" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    <a class="navbar-brand navbar-brand-custom navbar-brand-large mb-0" href="{{ route('home') }}">Jurassic Store</a>
                </div>
                <div class="user-account-summary">
                    <div class="user-account-meta">
                        <div class="user-account-name">{{ Auth::user()->name }}</div>
                        <div class="user-account-email">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="user-account-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        @php
            $currentPanel = $currentPanel ?? request()->query('panel', 'overview');
        @endphp

        <div class="container-fluid px-0">
            <div class="row gx-0">
                <!-- SIDEBAR DE CUENTA (sin bordes redondeados, altura completa) -->
                <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar p-0" style="min-height: calc(100vh - 80px); position:sticky; top:80px; border-radius:0;">
                    <div class="sidebar-sticky pt-4 px-3">
                        <ul class="nav flex-column gap-2">
                            <li class="nav-item">
                                <a class="nav-link sidebar-account-link {{ $currentPanel === 'overview' ? 'is-current' : '' }}"
                                   href="{{ route('user', ['panel' => 'overview']) }}"
                                   @if ($currentPanel === 'overview') aria-current="page" @endif
                                >
                                    <span class="me-2">🏠</span> Información general
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sidebar-account-link {{ $currentPanel === 'security' ? 'is-current' : '' }}"
                                   href="{{ route('user', ['panel' => 'security']) }}"
                                   @if ($currentPanel === 'security') aria-current="page" @endif
                                >
                                    <span class="me-2">🔒</span> Seguridad
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sidebar-account-link {{ $currentPanel === 'orders' ? 'is-current' : '' }}"
                                   href="{{ route('user', ['panel' => 'orders']) }}"
                                   @if ($currentPanel === 'orders') aria-current="page" @endif
                                >
                                    <span class="me-2">🧾</span> Pedidos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sidebar-account-link {{ $currentPanel === 'favorites' ? 'is-current' : '' }}"
                                   href="{{ route('user', ['panel' => 'favorites']) }}"
                                   @if ($currentPanel === 'favorites') aria-current="page" @endif
                                >
                                    <span class="me-2">⭐</span> Favoritos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link sidebar-account-link {{ $currentPanel === 'edit' ? 'is-current' : '' }}"
                                   href="{{ route('user', ['panel' => 'edit']) }}"
                                   @if ($currentPanel === 'edit') aria-current="page" @endif
                                >
                                    <span class="me-2">✏️</span> Editar perfil
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- CONTENIDO PRINCIPAL DE LA CUENTA (a la derecha del sidebar) -->
                <main class="col-md-9 col-lg-10 pt-4">
                    <div class="ps-md-5 ps-lg-5 pe-md-4 pe-lg-4">
                        @if (request()->query('verified') === '1' && $currentPanel === 'overview')
                            <div class="alert alert-success mb-4" role="alert">
                                Tu correo fue verificado correctamente.
                            </div>
                        @endif

                        @if ($currentPanel === 'overview')
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Información general</h2>

                                <div class="card shadow-sm p-4">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                                        <div>
                                            <h3 class="mb-2">Resumen de cuenta</h3>
                                            <p class="text-muted mb-0">Consulta tus datos principales y el estado actual de tu cuenta.</p>
                                        </div>

                                        <span class="badge text-bg-dark px-3 py-2">
                                            {{ Auth::user()->hasVerifiedEmail() ? 'Correo verificado' : 'Pendiente de verificación' }}
                                        </span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <p class="text-muted small mb-1">Nombre actual</p>
                                                <h3 class="h5 mb-0">{{ Auth::user()->name }}</h3>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <p class="text-muted small mb-1">Correo principal</p>
                                                <h3 class="h5 mb-0">{{ Auth::user()->email }}</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-4 border-top">
                                        <p class="text-muted mb-0">Si terminaste de revisar tu cuenta, puedes cerrar tu sesión desde aquí.</p>

                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-dark">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        @elseif ($currentPanel === 'orders')
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Pedidos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="{{ route('user') }}" class="mb-4">
                                        <input type="hidden" name="panel" value="orders">
                                        <div class="input-group">
                                            <input type="text"
                                                   name="orders_search"
                                                   value="{{ $ordersSearch ?? '' }}"
                                                   class="form-control"
                                                   placeholder="Buscar por ID de pedido o producto"
                                            >
                                            <button type="submit" class="btn btn-outline-dark">Buscar</button>
                                        </div>
                                    </form>

                                    @if ($orders !== null && $orders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Estado</th>
                                                        <th>Pedido</th>
                                                        <th>ID</th>
                                                        <th>Total</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($orders as $order)
                                                        @php
                                                            $primaryItem = $order->orderItems->first();
                                                            $primaryProduct = $primaryItem?->product?->name ?? 'Pedido sin productos';
                                                            $remainingItems = max($order->orderItems->count() - 1, 0);
                                                            $statusLabels = [
                                                                'completed' => 'Completado',
                                                                'pending' => 'Pendiente',
                                                                'cancelled' => 'Cancelado',
                                                            ];
                                                            $statusClasses = [
                                                                'completed' => 'text-success',
                                                                'pending' => 'text-warning-emphasis',
                                                                'cancelled' => 'text-danger',
                                                            ];
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                            <td class="fw-semibold {{ $statusClasses[$order->status] ?? 'text-body-secondary' }}">
                                                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                                            </td>
                                                            <td>
                                                                <div class="fw-semibold">{{ $primaryProduct }}</div>
                                                                @if ($remainingItems > 0)
                                                                    <div class="text-muted small">+ {{ $remainingItems }} producto(s) más</div>
                                                                @endif
                                                            </td>
                                                            <td>#{{ $order->id }}</td>
                                                            <td class="fw-semibold">${{ number_format((float) $order->total_price, 2) }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-dark">Detalles</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4">
                                            {{ $orders->links() }}
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0" role="alert">
                                            {{ ($ordersSearch ?? '') !== '' ? 'No encontramos pedidos con ese criterio.' : 'Todavía no tienes pedidos registrados.' }}
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @elseif ($currentPanel === 'favorites')
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Favoritos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="{{ route('user') }}" class="mb-4">
                                        <input type="hidden" name="panel" value="favorites">
                                        <div class="input-group">
                                            <input type="text"
                                                   name="favorites_search"
                                                   value="{{ $favoritesSearch ?? '' }}"
                                                   class="form-control"
                                                   placeholder="Buscar por nombre de producto"
                                            >
                                            <button type="submit" class="btn btn-outline-dark">Buscar</button>
                                        </div>
                                    </form>

                                    @if ($favorites !== null && $favorites->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Producto</th>
                                                        <th>Categoría</th>
                                                        <th>Stock</th>
                                                        <th>Precio</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($favorites as $favorite)
                                                        @php
                                                            $product = $favorite->product;
                                                            $categoryLabel = $product?->deepestCategories()->pluck('name')->implode(', ') ?: 'Sin categorías';
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                                <div class="text-muted small">ID producto #{{ $product->id }}</div>
                                                            </td>
                                                            <td>{{ $categoryLabel }}</td>
                                                            <td>{{ $product->stock }}</td>
                                                            <td class="fw-semibold">${{ number_format((float) $product->price, 2) }}</td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-dark">Ver</a>
                                                                    <form action="{{ route('favorites.remove', $product) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Quitar</button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4">
                                            {{ $favorites->links() }}
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0" role="alert">
                                            {{ ($favoritesSearch ?? '') !== '' ? 'No encontramos favoritos con ese criterio.' : 'Todavía no tienes productos guardados en favoritos.' }}
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @elseif ($currentPanel === 'edit')
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Editar perfil</h2>

                                <div class="card shadow-sm p-4" id="account-profile-form">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                                        <div>
                                            <h3 class="mb-2">Datos del perfil</h3>
                                            <p class="text-muted mb-0">Actualiza aquí tu nombre y tu correo principal.</p>
                                        </div>

                                        <span class="badge text-bg-dark px-3 py-2">Cuenta activa</span>
                                    </div>

                                    @if (session('status') === 'profile-updated')
                                        <div class="alert alert-success" role="alert">
                                            Tus datos se actualizaron correctamente.
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('profile.update') }}">
                                        @csrf
                                        @method('PATCH')

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label for="user-profile-name" class="form-label">Nombre</label>
                                                <input id="user-profile-name"
                                                       name="name"
                                                       type="text"
                                                       value="{{ old('name', Auth::user()->name) }}"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       required
                                                       autofocus
                                                       autocomplete="name"
                                                >
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-6">
                                                <label for="user-profile-email" class="form-label">Email</label>
                                                <input id="user-profile-email"
                                                       name="email"
                                                       type="email"
                                                       value="{{ old('email', Auth::user()->email) }}"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       required
                                                       autocomplete="username"
                                                >
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                                            <p class="text-muted mb-0">Estos datos identifican tu cuenta dentro de Jurassic Store.</p>

                                            <button type="submit" class="btn btn-warning fw-bold">
                                                Guardar cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </section>
                        @elseif ($currentPanel === 'security')
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Seguridad</h2>

                                <div class="card shadow-sm p-4">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                                        <div>
                                            <h3 class="mb-2">Seguridad de la cuenta</h3>
                                            <p class="text-muted mb-0">Cambia tu contraseña y gestiona acciones sensibles desde esta misma pantalla.</p>
                                        </div>

                                        <span class="badge text-bg-secondary px-3 py-2">Protección</span>
                                    </div>

                                    @if (session('status') === 'password-updated')
                                        <div class="alert alert-success" role="alert">
                                            Tu contraseña se actualizó correctamente.
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            <div class="col-lg-4">
                                                <label for="user-current-password" class="form-label">Contraseña actual</label>
                                                <input id="user-current-password"
                                                       name="current_password"
                                                       type="password"
                                                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                                       autocomplete="current-password"
                                                >
                                                @error('current_password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-new-password" class="form-label">Nueva contraseña</label>
                                                <input id="user-new-password"
                                                       name="password"
                                                       type="password"
                                                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                                       autocomplete="new-password"
                                                >
                                                @error('password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-password-confirmation" class="form-label">Confirmar contraseña</label>
                                                <input id="user-password-confirmation"
                                                       name="password_confirmation"
                                                       type="password"
                                                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                                       autocomplete="new-password"
                                                >
                                                @error('password_confirmation', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-warning fw-bold">
                                                Actualizar contraseña
                                            </button>
                                        </div>
                                    </form>

                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                            <div>
                                                <h4 class="h5 text-danger mb-1">Eliminar cuenta</h4>
                                                <p class="text-muted mb-0">
                                                    Esta acción eliminará tu cuenta de forma permanente. No se puede deshacer.
                                                </p>
                                            </div>

                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteAccountModal"
                                            >Eliminar cuenta</button>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif
                    </div>

                    <footer class="bg-black text-white py-4 mt-4">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>Sobre nosotros</h5>
                                    <p>Jurassic Store trae la emoción prehistórica a la vida con nuestra exclusiva colección de dinosaurios.</p>
                                </div>
                                <div class="col-md-4">
                                    <h5>Enlaces</h5>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('about') }}" class="text-white-50">Sobre nosotros</a></li>
                                        <li><a href="{{ route('contact') }}" class="text-white-50">Contacto</a></li>
                                        <li><a class="text-white-50">Política de privacidad</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h5>Síguenos</h5>
                                    <p class="text-white-50">¡Mantente actualizado con nuestros últimos descubrimientos de dinosaurios!</p>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center text-white-50">
                                <p>&copy; 2026 Jurassic Store. Todos los derechos reservados.</p>
                            </div>
                        </div>
                    </footer>
                </main>
            </div>
        </div>

        <div class="modal fade"
             id="deleteAccountModal"
             tabindex="-1"
             aria-labelledby="deleteAccountModalLabel"
             aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h2 class="modal-title h5 mb-0" id="deleteAccountModalLabel">Confirmar eliminación</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body">
                            <p class="mb-3">
                                Esta acción eliminará tu cuenta de forma permanente. Ingresa tu contraseña para continuar.
                            </p>

                            <div class="mb-3">
                                <label for="delete-account-password" class="form-label">Contraseña</label>
                                <input id="delete-account-password"
                                       name="password"
                                       type="password"
                                       class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                       autocomplete="current-password"
                                >
                                @error('password', 'userDeletion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Eliminar cuenta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($errors->userDeletion->isNotEmpty())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const deleteAccountModalElement = document.getElementById('deleteAccountModal');

                    if (deleteAccountModalElement) {
                        const deleteAccountModal = new bootstrap.Modal(deleteAccountModalElement);
                        deleteAccountModal.show();
                    }
                });
            </script>
        @endif

        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>

