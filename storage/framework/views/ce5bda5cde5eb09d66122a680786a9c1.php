



<?php $__env->startSection('content'); ?>
        
        <!-- offcanvas movil -->
        <div class="offcanvas offcanvas-start text-bg-dark account-sidebar-mobile d-md-none" tabindex="-1" id="accountSidebarMobile" aria-labelledby="accountSidebarMobileLabel">
            <div class="offcanvas-header">
                <div>
                    <h2 class="offcanvas-title h5 mb-1" id="accountSidebarMobileLabel">Mi cuenta</h2>
                    <div class="small text-white-50"><?php echo e(Auth::user()->email); ?></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>

            <div class="offcanvas-body">
                <?php echo $__env->make('profile.partials.account-nav-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row gx-0">

                <!--
                    SIDEBAR DESKTOP
                    Columna de navegación fija visible solo en pantallas medianas y grandes (d-none d-md-block). Ocupa 3 columnas en md y 2 en lg.
                    Comparte los links con el offcanvas mobile mediante el mismo @include, garantizando que ambos siempre estén sincronizados.
                    → resources/views/profile/partials/account-nav-links.blade.php
                -->
                <nav class="d-none d-md-block col-md-3 col-lg-2 sidebar p-0 account-sidebar">
                    <div class="sidebar-sticky pt-4 px-3">
                        <?php echo $__env->make('profile.partials.account-nav-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                        <?php if(request()->query('verified') === '1' && $currentPanel === 'overview'): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                Tu correo fue verificado correctamente.
                            </div>
                        <?php endif; ?>

                        <!--
                            SISTEMA DE PANELES
                            Un único bloque @if/@elseif renderiza solo el panel activo.
                            El controlador garantiza que $currentPanel siempre sea uno de los cinco valores válidos, por lo que no hay @else final.
                        -->
                        <?php if($currentPanel === 'overview'): ?>
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
                                            <?php echo e(Auth::user()->hasVerifiedEmail() ? 'Correo verificado' : 'Pendiente de verificación'); ?>

                                        </span>
                                    </div>

                                    

                                    <div class="border rounded-3 overflow-hidden">
                                        <div class="px-3 py-3 d-flex align-items-center gap-3">
                                            <?php if(Auth::user()->photo): ?>
                                                <a href="<?php echo e(asset('storage/' . Auth::user()->photo)); ?>" target="_blank" class="d-inline-block flex-shrink-0">
                                                    <img src="<?php echo e(asset('storage/' . Auth::user()->photo)); ?>" class="profile-overview-photo" alt="Foto de perfil">
                                                </a>
                                            <?php else: ?>
                                                <div class="profile-overview-avatar flex-shrink-0"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="text-muted small mb-1">Nombre actual</p>
                                                <p class="mb-0 fw-semibold"><?php echo e(Auth::user()->name); ?></p>
                                            </div>
                                        </div>
                                        <div class="border-top px-3 py-3">
                                            <p class="text-muted small mb-1">Correo principal</p>
                                            <p class="mb-0 fw-semibold"><?php echo e(Auth::user()->email); ?></p>
                                        </div>
                                        <div class="border-top px-3 py-3">
                                            <p class="text-muted small mb-1">Fecha de nacimiento</p>
                                            <?php if(Auth::user()->birthdate): ?>
                                                <p class="mb-0 fw-semibold">
                                                    <?php echo e(Auth::user()->birthdate->format('d/m/Y')); ?>

                                                    <small class="text-muted ms-2"><?php echo e(Auth::user()->birthdate->age); ?> años</small>
                                                </p>
                                            <?php else: ?>
                                                <p class="mb-0 text-muted">No especificada</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-4 border-top">
                                        <p class="text-muted mb-0">Si terminaste de revisar tu cuenta, puedes cerrar tu sesión desde aquí.</p>

                                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-danger">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        <?php elseif($currentPanel === 'orders'): ?>

                            
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Pedidos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="<?php echo e(route('user')); ?>" class="mb-4">
                                        <input type="hidden" name="panel" value="orders">
                                        <div class="input-group">
                                            <input type="text" name="orders_search" value="<?php echo e($ordersSearch ?? ''); ?>" class="form-control" placeholder="Buscar por ID de pedido o producto">
                                            <button type="submit" class="btn btn-outline-dark">Buscar</button>
                                        </div>
                                    </form>

                                    <?php if($orders !== null && $orders->count() > 0): ?>
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
                                                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $primaryItem = $order->orderItems->first();
                                                            $primaryProduct = $primaryItem?->product?->name ?? 'Pedido sin productos';
                                                            $remainingItems = max($order->orderItems->count() - 1, 0);
                                                        ?>
                                                        <tr>
                                                            <td><?php echo e($order->date->format('d/m/Y H:i')); ?></td>
                                                            <td class="fw-semibold <?php echo e($statusClasses[$order->status] ?? 'text-body-secondary'); ?>">
                                                                <?php echo e($statusLabels[$order->status] ?? ucfirst($order->status)); ?>

                                                            </td>
                                                            <td>
                                                                <div class="fw-semibold"><?php echo e($primaryProduct); ?></div>
                                                                <?php if($remainingItems > 0): ?>
                                                                    <div class="text-muted small">+ <?php echo e($remainingItems); ?> producto(s) más</div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>#<?php echo e($order->id); ?></td>
                                                            <td class="fw-semibold">$<?php echo e(number_format((float) $order->total_price, 2)); ?></td>
                                                            <td class="text-end">
                                                                <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-sm btn-outline-dark">Detalles</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4">
                                            <?php echo e($orders->links()); ?>

                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0" role="alert">
                                            <?php echo e(($ordersSearch ?? '') !== '' ? 'No encontramos pedidos con ese criterio.' : 'Todavía no tienes pedidos registrados.'); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                        <?php elseif($currentPanel === 'favorites'): ?>
                            
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 account-user-title">Favoritos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="<?php echo e(route('user')); ?>" class="mb-4">
                                        <input type="hidden" name="panel" value="favorites">
                                        <div class="input-group">
                                            <input type="text" name="favorites_search" value="<?php echo e($favoritesSearch ?? ''); ?>" class="form-control" placeholder="Buscar por nombre de producto">
                                            <button type="submit" class="btn btn-outline-dark">Buscar</button>
                                        </div>
                                    </form>

                                    <?php if($favorites !== null && $favorites->count() > 0): ?>
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
                                                    <?php $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $favorite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $product = $favorite->product;
                                                            $categoryLabel = $product?->deepestCategories()->pluck('name')->implode(', ') ?: 'Sin categorías';
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold"><?php echo e($product->name); ?></div>
                                                                <div class="text-muted small">ID producto #<?php echo e($product->id); ?></div>
                                                            </td>
                                                            <td><?php echo e($categoryLabel); ?></td>
                                                            <td><?php echo e($product->stock); ?></td>
                                                            <td class="fw-semibold">$<?php echo e(number_format((float) $product->price, 2)); ?></td>
                                                            <td class="text-end">
                                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                                    <a href="<?php echo e(route('products.show', $product)); ?>" class="btn btn-sm btn-outline-dark">Ver</a>
                                                                    <form action="<?php echo e(route('favorites.remove', $product)); ?>" method="POST" class="d-inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Quitar</button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4">
                                            <?php echo e($favorites->links()); ?>

                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0" role="alert">
                                            <?php echo e(($favoritesSearch ?? '') !== '' ? 'No encontramos favoritos con ese criterio.' : 'Todavía no tienes productos guardados en favoritos.'); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                        <?php elseif($currentPanel === 'edit'): ?>
                            
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

                                    <?php if(session('status') === 'profile-updated'): ?>
                                        <div class="alert alert-success" role="alert">
                                            Tus datos se actualizaron correctamente.
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <div class="mb-3">
                                            <label for="user-profile-name" class="form-label">Nombre</label>
                                            <input id="user-profile-name" name="name" type="text" value="<?php echo e(old('name', Auth::user()->name)); ?>" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autofocus autocomplete="name">
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-profile-email" class="form-label">Email</label>
                                            <input id="user-profile-email" name="email" type="email" value="<?php echo e(old('email', Auth::user()->email)); ?>" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autocomplete="username">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="user-profile-birthdate" class="form-label">Fecha de nacimiento</label>
                                            <input id="user-profile-birthdate" name="birthdate" type="date" value="<?php echo e(old('birthdate', Auth::user()->birthdate?->format('Y-m-d'))); ?>" class="form-control <?php $__errorArgs = ['birthdate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <?php $__errorArgs = ['birthdate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Foto de perfil</label>
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div id="avatar-preview-wrapper" style="width: 96px; height: 96px; border-radius: 50%; overflow: hidden; background: #f0f0f0; flex-shrink: 0; border: 2px solid rgba(0,0,0,0.1); position: relative;">
                                                    <img id="avatar_preview"
                                                         src="<?php echo e(Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : ''); ?>"
                                                         alt="Foto de perfil"
                                                         style="width: 100%; height: 100%; object-fit: cover; display: <?php echo e(Auth::user()->photo ? 'block' : 'none'); ?>;">
                                                    <div id="avatar-initials"
                                                         style="position: absolute; inset: 0; font-weight: 700; font-size: 2rem; color: #fff; background: #b5120f; display: <?php echo e(Auth::user()->photo ? 'none' : 'flex'); ?>; align-items: center; justify-content: center;">
                                                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

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
                                            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <?php elseif($currentPanel === 'security'): ?>
                            
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

                                    <?php if(session('status') === 'password-updated'): ?>
                                        <div class="alert alert-success" role="alert">
                                            Tu contraseña se actualizó correctamente.
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('password.update')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>

                                        <div class="row g-3">
                                            <div class="col-lg-4">
                                                <label for="user-current-password" class="form-label">Contraseña actual</label>
                                                <input id="user-current-password" name="current_password" type="password" class="form-control <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="current-password">
                                                <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-new-password" class="form-label">Nueva contraseña</label>
                                                <input id="user-new-password" name="password" type="password" class="form-control <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="new-password">
                                                <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="user-password-confirmation" class="form-label">Confirmar contraseña</label>
                                                <input id="user-password-confirmation" name="password_confirmation" type="password" class="form-control <?php $__errorArgs = ['password_confirmation', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="new-password">
                                                <?php $__errorArgs = ['password_confirmation', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <?php endif; ?>
                    </div>

                </main>
            </div>
        </div>

        
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h2 class="modal-title h5 mb-0" id="deleteAccountModalLabel">Confirmar eliminación</h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <form method="POST" action="<?php echo e(route('profile.destroy')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

                        <div class="modal-body">
                            <p class="mb-3">
                                Esta acción eliminará tu cuenta de forma permanente. Ingresa tu contraseña para continuar.
                            </p>

                            <div class="mb-3">
                                <label for="delete-account-password" class="form-label">Contraseña</label>
                                <input id="delete-account-password" name="password" type="password" class="form-control <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="current-password">
                                <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<?php $__env->stopSection(); ?>


<?php if($errors->userDeletion->isNotEmpty()): ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteAccountModalElement = document.getElementById('deleteAccountModal');

                if (deleteAccountModalElement) {
                    const deleteAccountModal = new bootstrap.Modal(deleteAccountModalElement);
                    deleteAccountModal.show();
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if($currentPanel === 'edit'): ?>
    <?php $__env->startPush('scripts'); ?>
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
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.account', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\profile\user.blade.php ENDPATH**/ ?>