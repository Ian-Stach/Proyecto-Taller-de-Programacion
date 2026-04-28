
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="carritoSidebar"
     aria-labelledby="carritoSidebarLabel"
>

    
    <div class="offcanvas-header bg-warning">
        <h5 class="offcanvas-title"
            id="carritoSidebarLabel"
        >🛒 Carrito de compras</h5>
        
        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Cerrar carrito"
        ></button>
    </div>

    
    <div class="offcanvas-body d-flex flex-column">

        
        <div class="cart-items flex-grow-1">
            <?php if(count($sidebarCartItems) > 0): ?>
                
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $sidebarCartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    
                                    <h6 class="mb-1"><?php echo e($item['product']->name); ?></h6>
                                    <small class="text-muted">Cant.: <?php echo e($item['quantity']); ?></small>
                                </div>

                                
                                <div class="d-flex flex-column align-items-end gap-1">
                                    
                                    <span class="text-warning fw-bold">$<?php echo e(number_format($item['subtotal'], 2)); ?></span>

                                    
                                    <form action="<?php echo e(route('cart.remove', $item['product'])); ?>"
                                          method="POST"
                                          class="cart-remove-form"
                                    >
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="btn btn-link p-0 border-0"
                                                aria-label="Eliminar <?php echo e($item['product']->name); ?> del carrito"
                                                title="Eliminar"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 height="24px"
                                                 viewBox="0 -960 960 960"
                                                 width="24px"
                                                 fill="#818181"
                                            >
                                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <hr class="my-3">

                
                <div class="mb-3">
                    
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span>$<?php echo e(number_format($sidebarCartSubtotal, 2)); ?></span>
                    </div>

                    
                    <div class="d-flex justify-content-between">
                        <strong>Impuesto (10%):</strong>
                        <span>$<?php echo e(number_format($sidebarCartSubtotal * 0.1, 2)); ?></span>
                    </div>

                    
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-6 fw-bold">
                        <strong>Total:</strong>
                        <span class="text-warning">$<?php echo e(number_format($sidebarCartSubtotal * 1.1, 2)); ?></span>
                    </div>
                </div>
            <?php else: ?>
                
                <p class="text-muted text-center py-5">Tu carrito está vacío</p>
            <?php endif; ?>
        </div>

        
        <div class="mt-auto d-flex flex-column gap-2">
            
            <?php if(count($sidebarCartItems) > 0): ?>
                <a href="<?php echo e(route('cart.show')); ?>"
                   class="btn btn-warning w-100"
                >Ver carrito</a>
            <?php endif; ?>

            
            <a href="<?php echo e(route('products.index')); ?>"
               class="btn btn-secondary w-100"
               data-bs-dismiss="offcanvas"
            >Seguir comprando</a>
        </div>
    </div>
</div><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/layouts/partials/cart-sidebar.blade.php ENDPATH**/ ?>