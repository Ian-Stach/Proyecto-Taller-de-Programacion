{{--
    VISTA: profile/user
    ─────────────────────────────────────────────────────────────────────────────
    Dashboard de cuenta del usuario autenticado. Extiende layouts/account,
    que provee el <head>, el header negro con avatar, la barra de navegación
    amarilla, el footer y el @stack('scripts').

    Sistema de paneles:
      La vista no tiene URL propia por panel; en cambio, usa un único parámetro
      ?panel= en la query string para decidir qué sección mostrar.
      El controlador (ProfileController@show) recibe, valida y normaliza ese
      parámetro antes de inyectarlo aquí como $currentPanel.

    Paneles disponibles:
      overview   → resumen de cuenta (nombre, email, estado de verificación)
      orders     → tabla paginada de pedidos con búsqueda
      favorites  → tabla paginada de favoritos con búsqueda
      edit       → formulario PATCH de nombre y email
      security   → formulario PUT de contraseña + zona de eliminación de cuenta

    Variables inyectadas por el controlador:
      $currentPanel    → panel activo validado ('overview' por defecto)
      $orders          → LengthAwarePaginator|null (solo si panel=orders)
      $ordersSearch    → string de búsqueda de pedidos
      $favorites       → LengthAwarePaginator|null (solo si panel=favorites)
      $favoritesSearch → string de búsqueda de favoritos
      $statusLabels    → array status → etiqueta legible para pedidos
      $statusClasses   → array status → clase CSS de color para pedidos

    Estructura en orden de renderizado:
      1. offcanvas mobile  → sidebar de navegación para pantallas pequeñas
      2. sidebar desktop   → columna de navegación fija (d-none d-md-block)
      3. main              → panel activo según $currentPanel
      4. modal             → confirmación de eliminación de cuenta (panel security)
      5. @push('scripts') → script de apertura automática del modal si hay errores
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.account')

@section('content')
        {{--
            OFFCANVAS MOBILE
            Panel lateral deslizante que aparece en pantallas pequeñas (< md).
            Se activa desde el botón hamburguesa del header del layout.
            Contiene el email del usuario como identificación y el menú de
            navegación compartido con el sidebar desktop via @include.
        --}}
        <!-- offcanvas movil -->
        <div class="offcanvas offcanvas-start text-bg-dark account-sidebar-mobile d-md-none"
             tabindex="-1"
             id="accountSidebarMobile"
             aria-labelledby="accountSidebarMobileLabel"
        >
            <div class="offcanvas-header">
                <div>
                    <h2 class="offcanvas-title h5 mb-1"
                        id="accountSidebarMobileLabel"
                    >Mi cuenta
                    </h2>
                    <div class="small text-white-50">{{ Auth::user()->email }}</div>
                </div>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Cerrar"
                ></button>
            </div>

            <div class="offcanvas-body">
                @include('profile.partials.account-nav-links')
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row gx-0">
                {{--
                    SIDEBAR DESKTOP
                    Columna de navegación fija visible solo en pantallas medianas y
                    grandes (d-none d-md-block). Ocupa 3 columnas en md y 2 en lg.
                    Comparte los links con el offcanvas mobile mediante el mismo
                    @include, garantizando que ambos siempre estén sincronizados.
                    → resources/views/profile/partials/account-nav-links.blade.php
                --}}
                <nav class="d-none d-md-block col-md-3 col-lg-2 bg-dark sidebar p-0 account-sidebar">
                    <div class="sidebar-sticky pt-4 px-3">
                        @include('profile.partials.account-nav-links')
                    </div>
                </nav>
                {{--
                    ÁREA DE CONTENIDO PRINCIPAL
                    Ocupa el espacio restante junto al sidebar (9/12 en md, 10/12 en lg).
                    Contiene el panel activo según $currentPanel y el padding lateral
                    que separa el contenido del sidebar.
                --}}
                <main class="col-12 col-md-9 col-lg-10 pt-4">
                    <div class="ps-md-5 ps-lg-5 pe-md-4 pe-lg-4">
                        {{--
                            Alerta de verificación de correo exitosa.
                            Se muestra solo cuando Laravel redirige con ?verified=1
                            tras confirmar el email, y únicamente en el panel overview.
                        --}}
                        @if (request()->query('verified') === '1' && $currentPanel === 'overview')
                            <div class="alert alert-success mb-4" role="alert">
                                Tu correo fue verificado correctamente.
                            </div>
                        @endif

                        {{--
                            SISTEMA DE PANELES
                            Un único bloque @if/@elseif renderiza solo el panel activo.
                            El controlador garantiza que $currentPanel siempre sea uno
                            de los cinco valores válidos, por lo que no hay @else final.
                        --}}
                        @if ($currentPanel === 'overview')
                            {{-- PANEL: Información general — nombre, email, estado del correo y logout --}}
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
                            {{--
                                PANEL: Pedidos
                                Tabla paginada (8 por página) con búsqueda por ID o nombre de producto.
                                $primaryProduct muestra el primer ítem del pedido; si hay más,
                                se indica con "+ N producto(s) más".
                                $statusLabels y $statusClasses vienen del controlador y se aplican
                                por cada fila sin recalcularse en el loop.
                            --}}
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
                            {{--
                                PANEL: Favoritos
                                Tabla paginada (8 por página) con búsqueda por nombre de producto.
                                $categoryLabel usa deepestCategories() del modelo Product, que devuelve
                                solo las categorías hoja (más específicas) sin hacer queries extra
                                gracias al eager loading 'product.categories' del controlador.
                                El botón "Quitar" envía DELETE a favorites.remove.
                            --}}
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
                            {{--
                                PANEL: Editar perfil
                                Formulario PATCH a profile.update para actualizar nombre y email.
                                Usa old() para repoblar los campos si la validación falla.
                            --}}
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
                            {{--
                                PANEL: Seguridad
                                Formulario PUT a password.update con error bag 'updatePassword'.
                                Incluye la zona de eliminación de cuenta, que abre el modal
                                #deleteAccountModal definido al final de esta sección.
                            --}}
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

                </main>
            </div>
        </div>

        {{--
            MODAL DE ELIMINACIÓN DE CUENTA
            Se renderiza siempre en el DOM pero solo es visible desde el panel
            'security' (el botón que lo abre está dentro de ese panel).
            Requiere la contraseña actual para confirmar la acción.
            Usa el error bag 'userDeletion' para mostrar errores de validación.
            Si hay errores en ese bag, el @push('scripts') al final lo abre
            automáticamente al cargar la página.
        --}}
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

@endsection

{{--
    Si la eliminación de cuenta falló por validación, el controlador redirige
    de vuelta con los errores en el bag 'userDeletion'. Este script reabre el
    modal automáticamente para que el usuario vea el mensaje de error sin tener
    que hacer clic de nuevo en el botón.
    Se coloca en @push('scripts') para ejecutarse después de Bootstrap JS.
--}}
@if ($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteAccountModalElement = document.getElementById('deleteAccountModal');

                if (deleteAccountModalElement) {
                    const deleteAccountModal = new bootstrap.Modal(deleteAccountModalElement);
                    deleteAccountModal.show();
                }
            });
        </script>
    @endpush
@endif
