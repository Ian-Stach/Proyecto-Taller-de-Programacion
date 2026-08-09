{{--
    VISTA: profile/user
    ─────────────────────────────────────────────────────────────────────────────
    Dashboard de cuenta del usuario autenticado. Extiende layouts/account,
    que provee el <head>, el header negro con avatar, la barra de navegación
    amarilla, el footer y el @@stack('scripts').

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
      5. @@push('scripts') → script de apertura automática del modal si hay errores
    ─────────────────────────────────────────────────────────────────────────────
--}}

@extends('layouts.account')

@section('content')
        {{--
            OFFCANVAS MOBILE
            Panel lateral deslizante que aparece en pantallas pequeñas (< md). Se activa desde el botón hamburguesa del header del layout.
            Contiene el email del usuario como identificación y el menú de navegación compartido con el sidebar desktop via @@include.
        --}}
        <!-- offcanvas movil -->
        <div class="offcanvas offcanvas-start text-bg-dark account-sidebar-mobile d-md-none" tabindex="-1" id="accountSidebarMobile" aria-labelledby="accountSidebarMobileLabel">
            <div class="offcanvas-header">
                <div>
                    <h2 class="offcanvas-title h5 mb-1" id="accountSidebarMobileLabel">Mi cuenta</h2>
                    <div class="small text-white-50">{{ Auth::user()->email }}</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>

            <div class="offcanvas-body">
                @include('profile.partials.account-nav-links')
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row gx-0">

                <!--
                    SIDEBAR DESKTOP
                    Columna de navegación fija visible solo en pantallas medianas y grandes (d-none d-md-block). Ocupa 3 columnas en md y 2 en lg.
                    Comparte los links con el offcanvas mobile mediante el mismo @@include, garantizando que ambos siempre estén sincronizados.
                    → resources/views/profile/partials/account-nav-links.blade.php
                -->
                <nav class="d-none d-md-block col-md-3 col-lg-2 sidebar p-0 account-sidebar">
                    <div class="sidebar-sticky pt-4 px-3">
                        @include('profile.partials.account-nav-links')
                    </div>
                </nav>
                
                <!--
                    ÁREA DE CONTENIDO PRINCIPAL
                    Ocupa el espacio restante junto al sidebar (9/12 en md, 10/12 en lg). Contiene el panel activo según $currentPanel y el padding lateral que separa el contenido del sidebar.
                -->
                <main class="col-12 col-md-9 col-lg-10 pt-4">
                    <div class="ps-md-5 ps-lg-5 pe-md-4 pe-lg-4">

                        <!--
                            Alerta de verificación de correo exitosa.
                            Se muestra solo cuando Laravel redirige con ?verified=1 tras confirmar el email, y únicamente en el panel overview.
                        -->
                        @if (request()->query('verified') === '1' && $currentPanel === 'overview')
                            <div class="alert alert-success mb-4" role="alert">
                                Tu correo fue verificado correctamente.
                            </div>
                        @endif

                        <!--
                            SISTEMA DE PANELES
                            Un único bloque @@if/@@elseif renderiza solo el panel activo.
                            El controlador garantiza que $currentPanel siempre sea uno de los cinco valores válidos, por lo que no hay @@else final.
                        -->
                        @if ($currentPanel === 'overview')
                            <!-- PANEL: Información general — nombre, email, estado del correo y logout -->
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Información general</h2>

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

                                    

                                    <div class="border rounded-3 overflow-hidden">
                                        <div class="px-3 py-3 d-flex align-items-center gap-3">
                                            @if(Auth::user()->photo)
                                                <a href="{{ asset('storage/' . Auth::user()->photo) }}" target="_blank" class="d-inline-block flex-shrink-0">
                                                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="profile-overview-photo" alt="Foto de perfil">
                                                </a>
                                            @else
                                                <div class="profile-overview-avatar flex-shrink-0">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <p class="text-muted small mb-1">Nombre actual</p>
                                                <p class="mb-0 fw-semibold">{{ Auth::user()->name }}</p>
                                            </div>
                                        </div>
                                        <div class="border-top px-3 py-3">
                                            <p class="text-muted small mb-1">Correo principal</p>
                                            <p class="mb-0 fw-semibold">{{ Auth::user()->email }}</p>
                                        </div>
                                        <div class="border-top px-3 py-3">
                                            <p class="text-muted small mb-1">Fecha de nacimiento</p>
                                            @if(Auth::user()->birthdate)
                                                <p class="mb-0 fw-semibold">
                                                    {{ Auth::user()->birthdate->format('d/m/Y') }}
                                                    <small class="text-muted ms-2">{{ Auth::user()->birthdate->age }} años</small>
                                                </p>
                                            @else
                                                <p class="mb-0 text-muted">No especificada</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-4 border-top">
                                        <p class="text-muted mb-0">Si terminaste de revisar tu cuenta, puedes cerrar tu sesión desde aquí.</p>

                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">
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
                                $primaryProduct muestra el primer ítem del pedido; si hay más, se indica con "+ N producto(s) más".
                                $statusLabels y $statusClasses vienen del controlador y se aplican por cada fila sin recalcularse en el loop.
                            --}}
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Pedidos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="{{ route('user') }}" class="mb-4">
                                        <input type="hidden" name="panel" value="orders">
                                        <div class="input-group">
                                            <input type="text" name="orders_search" value="{{ $ordersSearch ?? '' }}" class="form-control" placeholder="Buscar por ID de pedido o producto">
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
                                                            <td>{{ $order->date->format('d/m/Y H:i') }}</td>
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
                                solo las categorías hoja (más específicas) sin hacer queries extra gracias al eager loading 'product.categories' del controlador.
                                El botón "Quitar" envía DELETE a favorites.remove.
                            --}}
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Favoritos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="{{ route('user') }}" class="mb-4">
                                        <input type="hidden" name="panel" value="favorites">
                                        <div class="input-group">
                                            <input type="text" name="favorites_search" value="{{ $favoritesSearch ?? '' }}" class="form-control" placeholder="Buscar por nombre de producto">
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
                                Formulario PATCH a profile.update para actualizar nombre y email. Usa old() para repoblar los campos si la validación falla.
                            --}}
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Editar perfil</h2>

                                <div class="card shadow-sm p-4" id="account-profile-form">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                                        <div>
                                            <h3 class="mb-2">Datos del perfil</h3>
                                            <p class="text-muted mb-0">Actualiza aquí los datos de tu cuenta.</p>
                                        </div>

                                        <span class="badge text-bg-dark px-3 py-2">Cuenta activa</span>
                                    </div>

                                    @if (session('status') === 'profile-updated')
                                        <div class="alert alert-success" role="alert">
                                            Tus datos se actualizaron correctamente.
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')

                                        <div class="mb-3">
                                            <label for="user-profile-name" class="form-label">Nombre</label>
                                            <input id="user-profile-name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-profile-email" class="form-label">Email</label>
                                            <input id="user-profile-email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-profile-birthdate" class="form-label">Fecha de nacimiento</label>
                                            <input id="user-profile-birthdate" name="birthdate" type="date" value="{{ old('birthdate', Auth::user()->birthdate?->format('Y-m-d')) }}" class="form-control @error('birthdate') is-invalid @enderror">
                                            @error('birthdate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Foto de perfil</label>
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div id="avatar-preview-wrapper" style="width: 96px; height: 96px; border-radius: 50%; overflow: hidden; background: #f0f0f0; flex-shrink: 0; border: 2px solid rgba(0,0,0,0.1); position: relative;">
                                                    <img id="avatar_preview"
                                                         src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : '' }}"
                                                         alt="Foto de perfil"
                                                         style="width: 100%; height: 100%; object-fit: cover; display: {{ Auth::user()->photo ? 'block' : 'none' }};">
                                                    <div id="avatar-initials"
                                                         style="position: absolute; inset: 0; font-weight: 700; font-size: 2rem; color: #fff; background: #b5120f; display: {{ Auth::user()->photo ? 'none' : 'flex' }}; align-items: center; justify-content: center;">
                                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="d-flex gap-2 mb-1">
                                                        <button type="button" id="btn-select-photo" class="btn btn-outline-secondary btn-sm">Seleccionar imagen</button>
                                                        <button type="button" id="btn-remove-photo" class="btn btn-link btn-sm text-danger px-0">Eliminar</button>
                                                    </div>
                                                    <p class="small text-muted mb-0">JPG, PNG o GIF · máx. 2 MB</p>
                                                </div>
                                            </div>
                                            <input id="photo-input" type="file" name="photo" accept="image/*" style="display: none">
                                            <input type="hidden" id="remove-photo-input" name="remove_photo" value="0">
                                            @error('photo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
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
                                Incluye la zona de eliminación de cuenta, que abre el modal #deleteAccountModal definido al final de esta sección.
                            --}}
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Seguridad</h2>

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
                                                <input id="user-current-password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                                                @error('current_password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-new-password" class="form-label">Nueva contraseña</label>
                                                <input id="user-new-password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                                                @error('password', 'updatePassword')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-password-confirmation" class="form-label">Confirmar contraseña</label>
                                                <input id="user-password-confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
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

                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Eliminar cuenta</button>
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
            Se renderiza siempre en el DOM pero solo es visible desde el panel 'security' (el botón que lo abre está dentro de ese panel).
            Requiere la contraseña actual para confirmar la acción. Usa el error bag 'userDeletion' para mostrar errores de validación.
            Si hay errores en ese bag, el @@push('scripts') al final lo abre automáticamente al cargar la página.
        --}}
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
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
                                <input id="delete-account-password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" autocomplete="current-password">
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

        {{-- MODAL: Recortar foto de perfil --}}
        <div class="modal fade" id="cropAvatarModal" tabindex="-1" aria-labelledby="cropAvatarModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
                <div class="modal-content border-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cropAvatarModalLabel">Recortar foto de perfil</h5>
                        <button type="button" class="btn-close" id="btn-crop-close" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-0" style="background: #111;">
                        <canvas id="crop-canvas" style="display: block; margin: auto; cursor: move; touch-action: none;"></canvas>
                        <div class="px-3 pt-3 pb-2">
                            <label class="form-label text-white small mb-1">Zoom</label>
                            <input id="crop-scale" type="range" class="form-range" step="0.01">
                            <p class="small text-white-50 mb-0 mt-1">Arrastra para reencuadrar · desliza para hacer zoom.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btn-crop-cancel">Cancelar</button>
                        <button type="button" class="btn btn-warning fw-bold" id="btn-crop-confirm">Aplicar recorte</button>
                    </div>
                </div>
            </div>
        </div>

@endsection

{{--
    Si la eliminación de cuenta falló por validación, el controlador redirige de vuelta con los errores en el bag 'userDeletion'. Este script reabre el
    modal automáticamente para que el usuario vea el mensaje de error sin tener que hacer clic de nuevo en el botón.
    Se coloca en @@push('scripts') para ejecutarse después de Bootstrap JS.
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

@if ($currentPanel === 'edit')
    @push('scripts')
        <script>
        (function () {
            'use strict';

            // ─── Constants ───────────────────────────────────────────────────
            const CANVAS_SIZE  = 420;  // display canvas width/height (px)
            const CROP_RADIUS  = 175;  // circular guide radius (px)
            const OUTPUT_SIZE  = 512;  // exported image size (px)

            // ─── DOM refs ────────────────────────────────────────────────────
            const btnSelect      = document.getElementById('btn-select-photo');
            const btnRemove      = document.getElementById('btn-remove-photo');
            const photoInput     = document.getElementById('photo-input');
            const removeInput    = document.getElementById('remove-photo-input');
            const avatarImg      = document.getElementById('avatar_preview');
            const avatarInit     = document.getElementById('avatar-initials');
            const cropCanvas     = document.getElementById('crop-canvas');
            const cropScale      = document.getElementById('crop-scale');
            const btnCropClose   = document.getElementById('btn-crop-close');
            const btnCropCancel  = document.getElementById('btn-crop-cancel');
            const btnCropConfirm = document.getElementById('btn-crop-confirm');

            if (!cropCanvas) return;

            const ctx         = cropCanvas.getContext('2d');
            const cropModalEl = document.getElementById('cropAvatarModal');
            const cropModal   = new bootstrap.Modal(cropModalEl);

            // ─── State ───────────────────────────────────────────────────────
            let img = null;
            let scale = 1, minScale = 1;
            let ox = 0, oy = 0;
            let dragging = false, dragX0 = 0, dragY0 = 0;

            // ─── Canvas init ─────────────────────────────────────────────────
            cropCanvas.width  = CANVAS_SIZE;
            cropCanvas.height = CANVAS_SIZE;

            // ─── Helpers ─────────────────────────────────────────────────────
            function clamp() {
                const maxX = Math.max((img.width  * scale) / 2 - CROP_RADIUS, 0);
                const maxY = Math.max((img.height * scale) / 2 - CROP_RADIUS, 0);
                ox = Math.max(-maxX, Math.min(maxX, ox));
                oy = Math.max(-maxY, Math.min(maxY, oy));
            }

            function draw() {
                const cx = CANVAS_SIZE / 2, cy = CANVAS_SIZE / 2;
                const iw = img.width * scale, ih = img.height * scale;
                const ix = cx - iw / 2 + ox, iy = cy - ih / 2 + oy;

                ctx.clearRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);
                ctx.drawImage(img, ix, iy, iw, ih);

                // dark overlay with circular cutout (even-odd fill)
                ctx.save();
                ctx.fillStyle = 'rgba(0,0,0,0.62)';
                ctx.beginPath();
                ctx.rect(0, 0, CANVAS_SIZE, CANVAS_SIZE);
                ctx.arc(cx, cy, CROP_RADIUS, 0, Math.PI * 2, true);
                ctx.fill('evenodd');
                ctx.restore();

                // dashed circle border
                ctx.save();
                ctx.strokeStyle = 'rgba(255,255,255,0.75)';
                ctx.lineWidth = 2;
                ctx.setLineDash([6, 4]);
                ctx.beginPath();
                ctx.arc(cx, cy, CROP_RADIUS, 0, Math.PI * 2);
                ctx.stroke();
                ctx.restore();
            }

            function resetState(image) {
                img = image;
                minScale = Math.max(
                    (CROP_RADIUS * 2) / img.width,
                    (CROP_RADIUS * 2) / img.height
                );
                scale = minScale;
                ox = 0; oy = 0;
                cropScale.min   = minScale.toFixed(4);
                cropScale.max   = (minScale * 5).toFixed(4);
                cropScale.step  = '0.01';
                cropScale.value = minScale.toFixed(4);
            }

            // ─── File selection ───────────────────────────────────────────────
            btnSelect.addEventListener('click', () => photoInput.click());

            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    const image = new Image();
                    image.onload = () => {
                        resetState(image);
                        draw();
                        cropModal.show();
                    };
                    image.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });

            // ─── Zoom slider ──────────────────────────────────────────────────
            cropScale.addEventListener('input', function () {
                scale = parseFloat(this.value);
                clamp();
                draw();
            });

            // ─── Drag (mouse) ─────────────────────────────────────────────────
            cropCanvas.addEventListener('mousedown', e => {
                dragging = true;
                dragX0 = e.clientX - ox;
                dragY0 = e.clientY - oy;
                cropCanvas.style.cursor = 'grabbing';
            });
            window.addEventListener('mousemove', e => {
                if (!dragging) return;
                ox = e.clientX - dragX0;
                oy = e.clientY - dragY0;
                clamp();
                draw();
            });
            window.addEventListener('mouseup', () => {
                dragging = false;
                cropCanvas.style.cursor = 'move';
            });

            // ─── Drag (touch) ─────────────────────────────────────────────────
            cropCanvas.addEventListener('touchstart', e => {
                e.preventDefault();
                const t = e.touches[0];
                dragging = true;
                dragX0 = t.clientX - ox;
                dragY0 = t.clientY - oy;
            }, { passive: false });
            window.addEventListener('touchmove', e => {
                if (!dragging) return;
                const t = e.touches[0];
                ox = t.clientX - dragX0;
                oy = t.clientY - dragY0;
                clamp();
                draw();
            });
            window.addEventListener('touchend', () => { dragging = false; });

            // ─── Confirm crop ─────────────────────────────────────────────────
            btnCropConfirm.addEventListener('click', () => {
                const out  = document.createElement('canvas');
                out.width  = OUTPUT_SIZE;
                out.height = OUTPUT_SIZE;
                const octx = out.getContext('2d');

                // clip output to circle
                octx.beginPath();
                octx.arc(OUTPUT_SIZE / 2, OUTPUT_SIZE / 2, OUTPUT_SIZE / 2, 0, Math.PI * 2);
                octx.clip();

                // map the crop circle region back to source image coordinates
                const cx = CANVAS_SIZE / 2, cy = CANVAS_SIZE / 2;
                const iw = img.width * scale, ih = img.height * scale;
                const ix = cx - iw / 2 + ox, iy = cy - ih / 2 + oy;

                const srcX = ((cx - CROP_RADIUS) - ix) / scale;
                const srcY = ((cy - CROP_RADIUS) - iy) / scale;
                const srcW = (CROP_RADIUS * 2) / scale;
                const srcH = (CROP_RADIUS * 2) / scale;

                octx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, OUTPUT_SIZE, OUTPUT_SIZE);

                out.toBlob(blob => {
                    const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
                    const dt   = new DataTransfer();
                    dt.items.add(file);
                    photoInput.files = dt.files;

                    avatarImg.src          = URL.createObjectURL(blob);
                    avatarImg.style.display = 'block';
                    if (avatarInit) avatarInit.style.display = 'none';

                    removeInput.value = '0';
                    cropModal.hide();
                }, 'image/jpeg', 0.92);
            });

            // ─── Cancel crop ──────────────────────────────────────────────────
            function cancelCrop() {
                photoInput.value = '';
                img = null;
                cropModal.hide();
            }
            btnCropCancel.addEventListener('click', cancelCrop);
            btnCropClose.addEventListener('click', cancelCrop);

            // ─── Remove photo ─────────────────────────────────────────────────
            btnRemove.addEventListener('click', () => {
                photoInput.value        = '';
                avatarImg.src           = '';
                avatarImg.style.display = 'none';
                if (avatarInit) avatarInit.style.display = 'flex';
                removeInput.value = '1';
                img = null;
            });
        })();
        </script>
    @endpush
@endif
