@extends('admin.layout')
@section('title', "Orden #{{ $order->id }}")

@section('content')
    <h2 class="mb-4 admin-panel-title">Orden #{{ $order->id }}</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Detalles de la orden --}}
        <div class="col-8">
            <div class="stat-card">
                <h5 class="stat-table-header m-0 py-2 fs-4 justify-content-center">Productos</h5>
                <div class="stat-table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="stat-table">
                            <tr>
                                <th></th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                <tr>
                                    <td class="text-start">
                                        @if ($item->product)
                                            <a href="{{ route('admin.products.edit', $item->product) }}">
                                                <button class="admin-product-card-btn bg-info">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                        <path d="M440-183v-274L200-596v274l240 139Zm80 0 240-139v-274L520-457v274Zm-80 92L160-252q-19-11-29.5-29T120-321v-318q0-22 10.5-40t29.5-29l280-161q19-11 40-11t40 11l280 161q19 11 29.5 29t10.5 40v318q0 22-10.5 40T800-252L520-91q-19 11-40 11t-40-11Zm200-528 77-44-237-137-78 45 238 136Zm-160 93 78-45-237-137-78 45 237 137Z"/>
                                                    </svg>
                                                </button>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->product)
                                            <a class="text-decoration-none text-dark" href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a>
                                        @else
                                            <span class="text-muted fst-italic">Producto eliminado</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end"><strong>USD</strong> {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end"><strong>USD</strong> {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold"><strong>USD</strong> {{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="col-4 d-flex flex-column gap-3">

            {{-- Info del usuario --}}
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Cliente</h6>
                <p class="mb-1 fw-bold text-white">{{ $order->user->name }}</p>
                <p class="mb-0 text-secondary small">{{ $order->user->email }}</p>
                <a class="btn btn-sm btn-outline-secondary mt-2" href="{{ route('admin.users.edit', $order->user) }}">Ver usuario</a>
            </div>

            {{-- Info de la orden --}}
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Detalles</h6>
                <dl class="mb-0 text-white small">
                    <dt>Fecha</dt>
                    <dd>{{ $order->date->format('d/m/Y H:i') }}</dd>
                    <dt>Estado actual</dt>
                    <dd>
                        @php
                            $badge = match($order->status) {
                                'completado' => 'bg-success',
                                'cancelado'  => 'bg-danger',
                                default      => 'bg-warning text-dark',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($order->status) }}</span>
                    </dd>
                </dl>
            </div>

            {{-- Cambiar estado --}}
            <div class="stat-card p-3">
                <h6 class="text-white mb-2">Cambiar estado</h6>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <select class="form-select mb-2" name="status">
                        <option value="pendiente"  {{ $order->status === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                        <option value="completado" {{ $order->status === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado"  {{ $order->status === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    <button class="btn btn-secondary w-100" type="submit">Guardar</button>
                </form>
            </div>

            <a class="btn btn-outline-secondary" href="{{ route('admin.orders') }}">← Volver a órdenes</a>
        </div>
    </div>
@endsection
