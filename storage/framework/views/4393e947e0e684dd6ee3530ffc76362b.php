

<?php $__env->startSection('title', 'Métricas'); ?>

<?php $__env->startSection('content'); ?>
<h2 class="mb-4 admin-panel-title">Métricas</h2>


<?php if($noStockCount > 0 || $lowStockCount > 0 || $stalePendingOrders > 0): ?>
<div class="mb-4">
    <?php if($noStockCount > 0): ?>
        <div class="alert alert-danger py-2 mb-2">
            ⚠️ <strong><?php echo e($noStockCount); ?></strong> <?php echo e(Str::plural('producto', $noStockCount)); ?> sin stock.
        </div>
    <?php endif; ?>
    <?php if($lowStockCount > 0): ?>
        <div class="alert alert-warning py-2 mb-2">
            ⚠️ <strong><?php echo e($lowStockCount); ?></strong> <?php echo e(Str::plural('producto', $lowStockCount)); ?> con stock crítico (≤ <?php echo e($lowStockThreshold); ?> unidades).
        </div>
    <?php endif; ?>
    <?php if($stalePendingOrders > 0): ?>
        <div class="alert alert-warning py-2 mb-2">
            ⚠️ <strong><?php echo e($stalePendingOrders); ?></strong> <?php echo e(Str::plural('orden', $stalePendingOrders)); ?> pendiente hace más de 48 horas.
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Ventas y Órdenes</h5>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ventas hoy</div>
            <div class="text-success fs-4 fw-bold">$<?php echo e(number_format($salesToday, 2)); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ventas este mes</div>
            <div class="fs-4 fw-bold">$<?php echo e(number_format($salesThisMonth, 2)); ?></div>
            <?php if($salesGrowth !== null): ?>
                <div class="small <?php echo e($salesGrowth >= 0 ? 'text-success' : 'text-danger'); ?>">
                    <?php echo e($salesGrowth >= 0 ? '▲' : '▼'); ?> <?php echo e(abs($salesGrowth)); ?>% vs mes anterior
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ticket promedio</div>
            <div class="fs-4 fw-bold">$<?php echo e(number_format($avgTicket, 2)); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Mes anterior</div>
            <div class="fs-4 fw-bold">$<?php echo e(number_format($salesLastMonth, 2)); ?></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes completadas</div>
            <div class="fs-4 fw-bold text-success"><?php echo e($completedOrders); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes pendientes</div>
            <div class="fs-4 fw-bold text-warning"><?php echo e($pendingOrders); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes canceladas</div>
            <div class="fs-4 fw-bold text-danger"><?php echo e($cancelledOrders); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Pendientes &gt; 48 h</div>
            <div class="fs-4 fw-bold <?php echo e($stalePendingOrders > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e($stalePendingOrders); ?></div>
        </div>
    </div>
</div>


<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Productos e Inventario</h5>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Sin stock</div>
            <div class="fs-4 fw-bold <?php echo e($noStockCount > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e($noStockCount); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Stock bajo (≤ <?php echo e($lowStockThreshold); ?>)</div>
            <div class="fs-4 fw-bold <?php echo e($lowStockCount > 0 ? 'text-warning' : 'text-success'); ?>"><?php echo e($lowStockCount); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Valor del inventario</div>
            <div class="fs-4 fw-bold">$<?php echo e(number_format($inventoryValue, 2)); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Agregados este mes</div>
            <div class="fs-4 fw-bold"><?php echo e($addedThisMonth); ?></div>
        </div>
    </div>
</div>


<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Clientes y Usuarios</h5>
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Nuevos este mes</div>
            <div class="fs-4 fw-bold"><?php echo e($newUsersThisMonth); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Clientes activos</div>
            <div class="fs-4 fw-bold"><?php echo e($activeUsers); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Clientes recurrentes</div>
            <div class="fs-4 fw-bold"><?php echo e($recurringUsers); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Inactivos (sin órdenes)</div>
            <div class="fs-4 fw-bold"><?php echo e($inactiveUsers); ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Promedio órdenes/cliente</div>
            <div class="fs-4 fw-bold"><?php echo e($avgOrdersPerUser); ?></div>
        </div>
    </div>
    <?php if($topBuyer): ?>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Mayor comprador</div>
            <div class="fw-bold text-truncate"><?php echo e($topBuyer->name); ?></div>
            <div class="text-success fw-semibold">$<?php echo e(number_format($topBuyer->total_spent, 2)); ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>


<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Gráficos</h5>
<div class="row g-4 mb-4">
    
    <div class="col-12 col-lg-8">
        <div class="stat-card p-3 text-light h-100">
            <div class="fw-semibold mb-2">Productos vendidos por mes (últimos 12 meses)</div>
            <canvas class="text-light" id="chartSalesByMonth" height="280"></canvas>
        </div>
    </div>
    
    <div class="col-12 col-lg-4">
        <div class="stat-card p-3 text-light h-100">
            <div class="fw-semibold mb-2">Estado de órdenes</div>
            <canvas id="chartOrderStatus" height="180"></canvas>
        </div>
    </div>
    
    <div class="col-12">
        <div class="stat-card p-3 text-light">
            <div class="fw-semibold mb-2">Ventas por día (últimos 30 días)</div>
            <canvas id="chartSalesByDay" height="140"></canvas>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Top 10 productos más vendidos</div>
            <?php if($topProducts->isEmpty()): ?>
                <p class="text-muted small">Sin datos todavía.</p>
            <?php else: ?>
                <canvas id="chartTopProducts" height="220"></canvas>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Ventas por categoría</div>
            <?php if($salesByCategory->isEmpty()): ?>
                <p class="text-muted small">Sin datos todavía.</p>
            <?php else: ?>
                <canvas id="chartSalesByCategory" height="220"></canvas>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Nuevos usuarios por mes</div>
            <canvas id="chartNewUsers" height="180"></canvas>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Top 10 más vendidos</div>
            <?php if($topProducts->isEmpty()): ?>
                <p class="text-muted small">Sin datos todavía.</p>
            <?php else: ?>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-muted"><?php echo e($i + 1); ?></td>
                        <td><?php echo e($p->name); ?></td>
                        <td class="text-end fw-semibold"><?php echo e($p->total_sold); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">10 menos vendidos</div>
            <?php if($leastSoldProducts->isEmpty()): ?>
                <p class="text-muted small">Sin datos todavía.</p>
            <?php else: ?>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $leastSoldProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-muted"><?php echo e($i + 1); ?></td>
                        <td><?php echo e($p->name); ?></td>
                        <td class="text-end fw-semibold"><?php echo e($p->total_sold); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php if($lowStockList->isNotEmpty()): ?>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2 text-warning">Stock crítico (≤ <?php echo e($lowStockThreshold); ?> uds.)</div>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Producto</th><th class="text-end">Stock</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $lowStockList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($p->name); ?></td>
                        <td class="text-end fw-semibold <?php echo e($p->stock <= 3 ? 'text-danger' : 'text-warning'); ?>"><?php echo e($p->stock); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const palette = [
    '#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f',
    '#edc948','#b07aa1','#ff9da7','#9c755f','#bab0ac',
];

Chart.defaults.color = '#fff';
const monthRevenue = <?php echo json_encode($salesByMonthFilled, 15, 512) ?>;
const dayRevenue = <?php echo json_encode($salesByDayFilled, 15, 512) ?>;
// ── Ventas por mes ────────────────────────────────────────────────────────
new Chart(document.getElementById('chartSalesByMonth'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthLabels, 15, 512) ?>,
        datasets: [{
            label: 'Productos vendidos',
            data: <?php echo json_encode($productsSoldByMonthFilled, 15, 512) ?>,
            borderColor: '#4e79a7',
            backgroundColor: 'rgba(85, 231, 41, 0.33)',
            fill: true,
            tension: .3,
            pointRadius: 4,
        }]
    },
    options: { 
        plugins: { 
            legend: { 
                display: false
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const soldUnits = Number(context.raw ?? 0);
                        const revenue = Number(monthRevenue[context.dataIndex] ?? 0);

                        return [
                            `Productos vendidos: ${soldUnits}`,
                            `Ventas totales: $${revenue.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
                        ];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                },
                title: {
                    display: true,
                    text: 'Unidades vendidas'
                },
                grid: {
                    color: '#979797'
                }
            },
            x: {
                grid: {
                    color: '#979797'
                }
            }
        }
    }
});

// ── Ventas por día ────────────────────────────────────────────────────────
new Chart(document.getElementById('chartSalesByDay'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($dayLabels, 15, 512) ?>,
        datasets: [{
            label: 'Productos vendidos',
            data: <?php echo json_encode($productsSoldByDayFilled, 15, 512) ?>,
            backgroundColor: 'rgba(78,121,167,.7)',
        }]
    },
    options: {
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const soldUnits = Number(context.raw ?? 0);
                        const revenue = Number(dayRevenue[context.dataIndex] ?? 0);

                        return [
                            `Productos vendidos: ${soldUnits}`,
                            `Ventas totales: $${revenue.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
                        ];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                },
                title: {
                    display: true,
                    text: 'Unidades vendidas'
                },
                grid: {
                    color: '#979797'
                }
            },
            x: {
                grid: {
                    color: '#979797'
                }
            }
        }
    }
});

// ── Estado de órdenes ─────────────────────────────────────────────────────
new Chart(document.getElementById('chartOrderStatus'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($orderStatusCounts), 15, 512) ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($orderStatusCounts), 15, 512) ?>,
            backgroundColor: ['#f28e2b','#59a14f','#e15759'],
            borderColor: '#c6c6c6',
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// ── Top productos ─────────────────────────────────────────────────────────
<?php if($topProducts->isNotEmpty()): ?>
new Chart(document.getElementById('chartTopProducts'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($topProducts->pluck('name'), 15, 512) ?>,
        datasets: [{
            label: 'Unidades vendidas',
            data: <?php echo json_encode($topProducts->pluck('total_sold'), 15, 512) ?>,
            backgroundColor: palette,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
<?php endif; ?>

// ── Ventas por categoría ──────────────────────────────────────────────────
<?php if($salesByCategory->isNotEmpty()): ?>
new Chart(document.getElementById('chartSalesByCategory'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($salesByCategory->pluck('name'), 15, 512) ?>,
        datasets: [{
            data: <?php echo json_encode($salesByCategory->pluck('total'), 15, 512) ?>,
            backgroundColor: palette,
        }]
    },
    options: { plugins: { legend: { position: 'right' } } }
});
<?php endif; ?>

// ── Nuevos usuarios ───────────────────────────────────────────────────────
new Chart(document.getElementById('chartNewUsers'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthLabels, 15, 512) ?>,
        datasets: [{
            label: 'Nuevos usuarios',
            data: <?php echo json_encode($newUsersByMonthFilled, 15, 512) ?>,
            backgroundColor: 'rgba(89,161,79,.75)',
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\admin\metrics.blade.php ENDPATH**/ ?>