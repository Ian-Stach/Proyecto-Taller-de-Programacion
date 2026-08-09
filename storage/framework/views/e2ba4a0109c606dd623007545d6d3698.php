

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="mb-4 admin-panel-title">Dashboard</h2>

    
    <div class="row g-3 mb-4 justify-content-center">

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M216-96q-29.7 0-50.85-21.15Q144-138.3 144-168v-412q-21-8-34.5-26.5T96-648v-144q0-29.7 21.15-50.85Q138.3-864 168-864h624q29.7 0 50.85 21.15Q864-821.7 864-792v144q0 23-13.5 41.5T816-580v411.86Q816-138 794.85-117T744-96H216Zm0-480v408h528v-408H216Zm-48-72h624v-144H168v144Zm216 240h192v-72H384v72Zm96 36Z"/>
                    </svg>
                Productos totales
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data"><?php echo e($stats['products']); ?></div>
                </div>
                <div class="stat-card-footer"><?php echo e($stats['products_active']); ?> activos</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/>
                </svg>
                    Categorías
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data"><?php echo e($stats['categories']); ?></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M96-192v-92q0-26 12.5-47.5T143-366q54-32 115-49t126-17q23 0 46.5 3t49.5 9l-61 62q-15-2-22-2h-13q-53 0-105 14t-100 42q-4.95 2.94-7.98 8.24Q168-290.47 168-284v20h286l71 72H96Zm530 0L491-328l50-51 85 85 187-186 51 50-238 238ZM282-522q-42-42-42-102t42-102q42-42 102-42t102 42q42 42 42 102t-42 102q-42 42-102 42t-102-42Zm172 258Zm-19-309.21q21-21.21 21-51T434.79-675q-21.21-21-51-21T333-674.79q-21 21.21-21 51T333.21-573q21.21 21 51 21T435-573.21ZM384-624Z"/>
                </svg>
                    Usuarios registrados
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data"><?php echo e($stats['users']); ?></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M144-96v-768l56 45 56-45 56 45 56-45 56 45 56-45 56 45 56-45 56 45 56-45 56 45 56-45v768l-56-45-56 45-56-45-56 45-56-45-56 45-56-45-56 45-56-45-56 45-56-45-56 45Zm144-216h384v-72H288v72Zm0-132h384v-72H288v72Zm0-132h384v-72H288v72Zm-72 360h528v-528H216v528Zm0-528v528-528Z"/>
                    </svg>
                    Órdenes totales
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data"><?php echo e($stats['orders']); ?></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="stat-card h-100">
                <div class="stat-card-header p-0 pt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M648-156h48v-168h-48v168Zm96 0h48v-168h-48v168ZM288-576h384v-72H288v72ZM719.77-48Q640-48 584-104.23q-56-56.22-56-136Q528-320 584.23-376q56.22-56 136-56Q800-432 856-375.77q56 56.22 56 136Q912-160 855.77-104q-56.22 56-136 56ZM144-96v-648q0-29.7 21.15-50.85Q186.3-816 216-816h528q29.7 0 50.85 21.15Q816-773.7 816-744v259q-17-7-35.03-11-18.04-4-36.97-6v-242H216v528h240q3 30 12.12 58T492-105l-12 9-56-45-56 45-56-45-56 45-56-45-56 45Zm144-216h177q5-20 13.5-37.5T498-384H288v72Zm0-132h264q26-21 56-35t64-20v-17H288v72Zm-72 228v-528 528Z"/>
                    </svg>
                    Órdenes pendientes
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data"><?php echo e($stats['pending_orders']); ?></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-5">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M444-144v-80q-51-11-87.5-46T305-357l74-30q8 36 40.5 64.5T487-294q39 0 64-20t25-52q0-30-22.5-50T474-456q-78-28-114-61.5T324-604q0-50 32.5-86t87.5-47v-79h72v79q72 12 96.5 55t25.5 45l-70 29q-8-26-32-43t-53-17q-35 0-58 18t-23 44q0 26 25 44.5t93 41.5q70 23 102 60t32 94q0 57-37 96t-101 49v77h-72Z"/>
                </svg>
                    Ingresos totales
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data">USD$<?php echo e(number_format($stats['orders_revenue'], 2)); ?></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-5">
            <div class="stat-card h-100">
                <div class="stat-card-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#b2b2b2">
                        <path d="M444-189v-270L216-591v270l228 132Zm72 0 228-131v-270L516-459v270Zm-72 84L180-258q-17.1-9.88-26.55-26.06Q144-300.23 144-320v-320q0-19.77 9.45-35.94Q162.9-692.12 180-702l264-153q17.13-10 36.07-10Q499-865 516-855l264 153q17.1 9.88 26.55 26.06Q816-659.77 816-640v320q0 19.77-9.45 35.94Q797.1-267.88 780-258L516-105q-17.13 10-36.07 10Q461-95 444-105Zm188-505 83-47-236-135-80 47 233 135Zm-152 88 82-47-237-134-80 46 235 135Z"/>
                    </svg>
                    Valor total del inventario
                </div>
                <div class="stat-card-body">
                    <div class="stat-card-data">USD$<?php echo e(number_format($stats['products_value'], 2)); ?></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 align-items-stretch">
        <div class="col-12 col-xl-8">
            <div class="stat-card h-100">
                <div class="stat-table-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                        <path d="m612-292 56-56-148-148v-184h-80v216l172 172ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-400Zm0 320q133 0 226.5-93.5T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160Z"/>
                    </svg>                    
                    Órdenes recientes
                </div>
                <div class="stat-table-responsive">
                    <table class="stat-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($order->id); ?></td>
                                    <td><?php echo e($order->user->name ?? '—'); ?></td>
                                    <td>$<?php echo e(number_format($order->total_price, 2)); ?></td>
                                    <td><?php echo e($order->date->format('d/m/Y')); ?></td>
                                    <td class="status-badge status-<?php echo e(strtolower($order->status)); ?>"><?php echo e($order->status); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-muted" colspan="4">Sin órdenes aún.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-12 col-xl-4">
            <div class="stat-card h-100">
                <div class="stat-table-header">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#fff">
                        <path d="M640-240v-80h104L536-526 376-366 80-664l56-56 240 240 160-160 264 264v-104h80v240H640Z"/>
                    </svg>
                    Bajo stock (≤ 5 unidades)
                </div>
                <div class="stat-table-responsive">
                    <table class="stat-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $lowStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>"><?php echo e($product->name); ?></a>
                                    </td>
                                    <td>
                                        <span class="stock-badge <?php echo e($product->stock === 0 ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                                            <?php echo e($product->stock); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-muted" colspan="2"
                                    >Todo bien con el stock.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>