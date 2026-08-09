@extends('admin.layout')

@section('title', 'Métricas')

@section('content')
<h2 class="mb-4 admin-panel-title">Métricas</h2>

{{-- ══════════════════════════════════════════════════════════
     🔔 ALERTAS RÁPIDAS
══════════════════════════════════════════════════════════ --}}
@if($noStockCount > 0 || $lowStockCount > 0 || $stalePendingOrders > 0)
<div class="mb-4">
    @if($noStockCount > 0)
        <div class="alert alert-danger py-2 mb-2">
            ⚠️ <strong>{{ $noStockCount }}</strong> {{ Str::plural('producto', $noStockCount) }} sin stock.
        </div>
    @endif
    @if($lowStockCount > 0)
        <div class="alert alert-warning py-2 mb-2">
            ⚠️ <strong>{{ $lowStockCount }}</strong> {{ Str::plural('producto', $lowStockCount) }} con stock crítico (≤ {{ $lowStockThreshold }} unidades).
        </div>
    @endif
    @if($stalePendingOrders > 0)
        <div class="alert alert-warning py-2 mb-2">
            ⚠️ <strong>{{ $stalePendingOrders }}</strong> {{ Str::plural('orden', $stalePendingOrders) }} pendiente hace más de 48 horas.
        </div>
    @endif
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     🛒 VENTAS Y ÓRDENES
══════════════════════════════════════════════════════════ --}}
<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Ventas y Órdenes</h5>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ventas hoy</div>
            <div class="text-success fs-4 fw-bold">${{ number_format($salesToday, 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ventas este mes</div>
            <div class="fs-4 fw-bold">${{ number_format($salesThisMonth, 2) }}</div>
            @if($salesGrowth !== null)
                <div class="small {{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $salesGrowth >= 0 ? '▲' : '▼' }} {{ abs($salesGrowth) }}% vs mes anterior
                </div>
            @endif
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Ticket promedio</div>
            <div class="fs-4 fw-bold">${{ number_format($avgTicket, 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Mes anterior</div>
            <div class="fs-4 fw-bold">${{ number_format($salesLastMonth, 2) }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes completadas</div>
            <div class="fs-4 fw-bold text-success">{{ $completedOrders }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes pendientes</div>
            <div class="fs-4 fw-bold text-warning">{{ $pendingOrders }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Órdenes canceladas</div>
            <div class="fs-4 fw-bold text-danger">{{ $cancelledOrders }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Pendientes &gt; 48 h</div>
            <div class="fs-4 fw-bold {{ $stalePendingOrders > 0 ? 'text-danger' : 'text-success' }}">{{ $stalePendingOrders }}</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     📦 PRODUCTOS E INVENTARIO
══════════════════════════════════════════════════════════ --}}
<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Productos e Inventario</h5>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Sin stock</div>
            <div class="fs-4 fw-bold {{ $noStockCount > 0 ? 'text-danger' : 'text-success' }}">{{ $noStockCount }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Stock bajo (≤ {{ $lowStockThreshold }})</div>
            <div class="fs-4 fw-bold {{ $lowStockCount > 0 ? 'text-warning' : 'text-success' }}">{{ $lowStockCount }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Valor del inventario</div>
            <div class="fs-4 fw-bold">${{ number_format($inventoryValue, 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Agregados este mes</div>
            <div class="fs-4 fw-bold">{{ $addedThisMonth }}</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     👥 CLIENTES
══════════════════════════════════════════════════════════ --}}
<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Clientes y Usuarios</h5>
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Nuevos este mes</div>
            <div class="fs-4 fw-bold">{{ $newUsersThisMonth }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Clientes activos</div>
            <div class="fs-4 fw-bold">{{ $activeUsers }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Clientes recurrentes</div>
            <div class="fs-4 fw-bold">{{ $recurringUsers }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Inactivos (sin órdenes)</div>
            <div class="fs-4 fw-bold">{{ $inactiveUsers }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Promedio órdenes/cliente</div>
            <div class="fs-4 fw-bold">{{ $avgOrdersPerUser }}</div>
        </div>
    </div>
    @if($topBuyer)
    <div class="col-6 col-md-3">
        <div class="stat-card h-100 text-center p-3 text-light">
            <div class="small">Mayor comprador</div>
            <div class="fw-bold text-truncate">{{ $topBuyer->name }}</div>
            <div class="text-success fw-semibold">${{ number_format($topBuyer->total_spent, 2) }}</div>
        </div>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     📈 GRÁFICOS
══════════════════════════════════════════════════════════ --}}
<h5 class="text-light text-uppercase fw-semibold mb-3 fs-3" style="letter-spacing:.08em;">Gráficos</h5>
<div class="row g-4 mb-4">
    {{-- Ventas últimos 12 meses --}}
    <div class="col-12 col-lg-8">
        <div class="stat-card p-3 text-light h-100">
            <div class="fw-semibold mb-2">Productos vendidos por mes (últimos 12 meses)</div>
            <canvas class="text-light" id="chartSalesByMonth" height="280"></canvas>
        </div>
    </div>
    {{-- Estado de órdenes --}}
    <div class="col-12 col-lg-4">
        <div class="stat-card p-3 text-light h-100">
            <div class="fw-semibold mb-2">Estado de órdenes</div>
            <canvas id="chartOrderStatus" height="180"></canvas>
        </div>
    </div>
    {{-- Ventas últimos 30 días --}}
    <div class="col-12">
        <div class="stat-card p-3 text-light">
            <div class="fw-semibold mb-2">Ventas por día (últimos 30 días)</div>
            <canvas id="chartSalesByDay" height="140"></canvas>
        </div>
    </div>
    {{-- Top 10 más vendidos --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Top 10 productos más vendidos</div>
            @if($topProducts->isEmpty())
                <p class="text-muted small">Sin datos todavía.</p>
            @else
                <canvas id="chartTopProducts" height="220"></canvas>
            @endif
        </div>
    </div>
    {{-- Ventas por categoría --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Ventas por categoría</div>
            @if($salesByCategory->isEmpty())
                <p class="text-muted small">Sin datos todavía.</p>
            @else
                <canvas id="chartSalesByCategory" height="220"></canvas>
            @endif
        </div>
    </div>
    {{-- Nuevos usuarios por mes --}}
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Nuevos usuarios por mes</div>
            <canvas id="chartNewUsers" height="180"></canvas>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     TABLAS: TOP / LEAST SOLD / STOCK BAJO
══════════════════════════════════════════════════════════ --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">Top 10 más vendidos</div>
            @if($topProducts->isEmpty())
                <p class="text-muted small">Sin datos todavía.</p>
            @else
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                <tbody>
                    @foreach($topProducts as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>{{ $p->name }}</td>
                        <td class="text-end fw-semibold">{{ $p->total_sold }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2">10 menos vendidos</div>
            @if($leastSoldProducts->isEmpty())
                <p class="text-muted small">Sin datos todavía.</p>
            @else
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                <tbody>
                    @foreach($leastSoldProducts as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>{{ $p->name }}</td>
                        <td class="text-end fw-semibold">{{ $p->total_sold }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    @if($lowStockList->isNotEmpty())
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="fw-semibold mb-2 text-warning">Stock crítico (≤ {{ $lowStockThreshold }} uds.)</div>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Producto</th><th class="text-end">Stock</th></tr></thead>
                <tbody>
                    @foreach($lowStockList as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td class="text-end fw-semibold {{ $p->stock <= 3 ? 'text-danger' : 'text-warning' }}">{{ $p->stock }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const palette = [
    '#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f',
    '#edc948','#b07aa1','#ff9da7','#9c755f','#bab0ac',
];

Chart.defaults.color = '#fff';
const monthRevenue = @json($salesByMonthFilled);
const dayRevenue = @json($salesByDayFilled);
// ── Ventas por mes ────────────────────────────────────────────────────────
new Chart(document.getElementById('chartSalesByMonth'), {
    type: 'line',
    data: {
        labels: @json($monthLabels),
        datasets: [{
            label: 'Productos vendidos',
            data: @json($productsSoldByMonthFilled),
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
        labels: @json($dayLabels),
        datasets: [{
            label: 'Productos vendidos',
            data: @json($productsSoldByDayFilled),
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
        labels: @json(array_keys($orderStatusCounts)),
        datasets: [{
            data: @json(array_values($orderStatusCounts)),
            backgroundColor: ['#f28e2b','#59a14f','#e15759'],
            borderColor: '#c6c6c6',
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// ── Top productos ─────────────────────────────────────────────────────────
@if($topProducts->isNotEmpty())
new Chart(document.getElementById('chartTopProducts'), {
    type: 'bar',
    data: {
        labels: @json($topProducts->pluck('name')),
        datasets: [{
            label: 'Unidades vendidas',
            data: @json($topProducts->pluck('total_sold')),
            backgroundColor: palette,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } }
    }
});
@endif

// ── Ventas por categoría ──────────────────────────────────────────────────
@if($salesByCategory->isNotEmpty())
new Chart(document.getElementById('chartSalesByCategory'), {
    type: 'pie',
    data: {
        labels: @json($salesByCategory->pluck('name')),
        datasets: [{
            data: @json($salesByCategory->pluck('total')),
            backgroundColor: palette,
        }]
    },
    options: { plugins: { legend: { position: 'right' } } }
});
@endif

// ── Nuevos usuarios ───────────────────────────────────────────────────────
new Chart(document.getElementById('chartNewUsers'), {
    type: 'bar',
    data: {
        labels: @json($monthLabels),
        datasets: [{
            label: 'Nuevos usuarios',
            data: @json($newUsersByMonthFilled),
            backgroundColor: 'rgba(89,161,79,.75)',
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endpush
