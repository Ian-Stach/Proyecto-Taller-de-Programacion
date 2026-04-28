


<?php $__env->startSection('content'); ?>
        
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
                    <div class="small text-white-50"><?php echo e(Auth::user()->email); ?></div>
                </div>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Cerrar"
                ></button>
            </div>

            <div class="offcanvas-body">
                <?php echo $__env->make('profile.partials.account-nav-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

        <div class="container-fluid px-0">
            <div class="row gx-0">
                
                <nav class="d-none d-md-block col-md-3 col-lg-2 bg-dark sidebar p-0 account-sidebar">
                    <div class="sidebar-sticky pt-4 px-3">
                        <?php echo $__env->make('profile.partials.account-nav-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </nav>
                
                <main class="col-12 col-md-9 col-lg-10 pt-4">
                    <div class="ps-md-5 ps-lg-5 pe-md-4 pe-lg-4">
                        
                        <?php if(request()->query('verified') === '1' && $currentPanel === 'overview'): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                Tu correo fue verificado correctamente.
                            </div>
                        <?php endif; ?>

                        
                        <?php if($currentPanel === 'overview'): ?>
                            
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Información general</h2>

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

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <p class="text-muted small mb-1">Nombre actual</p>
                                                <h3 class="h5 mb-0"><?php echo e(Auth::user()->name); ?></h3>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <p class="text-muted small mb-1">Correo principal</p>
                                                <h3 class="h5 mb-0"><?php echo e(Auth::user()->email); ?></h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-4 border-top">
                                        <p class="text-muted mb-0">Si terminaste de revisar tu cuenta, puedes cerrar tu sesión desde aquí.</p>

                                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-dark">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </section>
                        <?php elseif($currentPanel === 'orders'): ?>
                            
                            <section class="account-user-section mb-4">
                                <h2 class="mb-4 static-page-title">Pedidos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="<?php echo e(route('user')); ?>" class="mb-4">
                                        <input type="hidden" name="panel" value="orders">
                                        <div class="input-group">
                                            <input type="text"
                                                   name="orders_search"
                                                   value="<?php echo e($ordersSearch ?? ''); ?>"
                                                   class="form-control"
                                                   placeholder="Buscar por ID de pedido o producto"
                                            >
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
                                                            <td><?php echo e($order->created_at->format('d/m/Y H:i')); ?></td>
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
                                <h2 class="mb-4 static-page-title">Favoritos</h2>

                                <div class="card shadow-sm p-4">
                                    <form method="GET" action="<?php echo e(route('user')); ?>" class="mb-4">
                                        <input type="hidden" name="panel" value="favorites">
                                        <div class="input-group">
                                            <input type="text"
                                                   name="favorites_search"
                                                   value="<?php echo e($favoritesSearch ?? ''); ?>"
                                                   class="form-control"
                                                   placeholder="Buscar por nombre de producto"
                                            >
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
                                <h2 class="mb-4 static-page-title">Editar perfil</h2>

                                <div class="card shadow-sm p-4" id="account-profile-form">
                                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
                                        <div>
                                            <h3 class="mb-2">Datos del perfil</h3>
                                            <p class="text-muted mb-0">Actualiza aquí tu nombre y tu correo principal.</p>
                                        </div>

                                        <span class="badge text-bg-dark px-3 py-2">Cuenta activa</span>
                                    </div>

                                    <?php if(session('status') === 'profile-updated'): ?>
                                        <div class="alert alert-success" role="alert">
                                            Tus datos se actualizaron correctamente.
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label for="user-profile-name" class="form-label">Nombre</label>
                                                <input id="user-profile-name"
                                                       name="name"
                                                       type="text"
                                                       value="<?php echo e(old('name', Auth::user()->name)); ?>"
                                                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       required
                                                       autofocus
                                                       autocomplete="name"
                                                >
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

                                            <div class="col-lg-6">
                                                <label for="user-profile-email" class="form-label">Email</label>
                                                <input id="user-profile-email"
                                                       name="email"
                                                       type="email"
                                                       value="<?php echo e(old('email', Auth::user()->email)); ?>"
                                                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       required
                                                       autocomplete="username"
                                                >
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
                                <h2 class="mb-4 static-page-title">Seguridad</h2>

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
                                                <input id="user-current-password"
                                                       name="current_password"
                                                       type="password"
                                                       class="form-control <?php $__errorArgs = ['current_password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       autocomplete="current-password"
                                                >
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
                                                <input id="user-new-password"
                                                       name="password"
                                                       type="password"
                                                       class="form-control <?php $__errorArgs = ['password', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       autocomplete="new-password"
                                                >
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
                                                <input id="user-password-confirmation"
                                                       name="password_confirmation"
                                                       type="password"
                                                       class="form-control <?php $__errorArgs = ['password_confirmation', 'updatePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                       autocomplete="new-password"
                                                >
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

                                            <button type="button"
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteAccountModal"
                                            >Eliminar cuenta</button>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>
                    </div>

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

                    <form method="POST" action="<?php echo e(route('profile.destroy')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

                        <div class="modal-body">
                            <p class="mb-3">
                                Esta acción eliminará tu cuenta de forma permanente. Ingresa tu contraseña para continuar.
                            </p>

                            <div class="mb-3">
                                <label for="delete-account-password" class="form-label">Contraseña</label>
                                <input id="delete-account-password"
                                       name="password"
                                       type="password"
                                       class="form-control <?php $__errorArgs = ['password', 'userDeletion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       autocomplete="current-password"
                                >
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

<?php echo $__env->make('layouts.account', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/profile/user.blade.php ENDPATH**/ ?>