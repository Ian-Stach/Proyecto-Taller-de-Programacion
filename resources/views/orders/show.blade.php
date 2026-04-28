@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>📦 Pedido #{{ $order->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            @if($order->status === 'completed')
                <span class="badge bg-success fs-5">✅ Completado</span>
            @elseif($order->status === 'pending')
                <span class="badge bg-warning text-dark fs-5">⏳ Pendiente</span>
            @else
                <span class="badge bg-danger fs-5">❌ Cancelado</span>
            @endif
        </div>
    </div>

    <!-- Información del pedido -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <strong>Información del pedido</strong>
                </div>
                <div class="card-body">
                    <p><strong>Pedido:</strong> #{{ $order->id }}</p>
                    <p><strong>Fecha:</strong> {{ $order->created_at->translatedFormat('d M Y H:i') }}</p>
                    <p><strong>Cliente:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Correo electrónico:</strong> {{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        @php
            $subtotal = $order->orderItems->sum(function($item) {
                return $item->unit_price * $item->quantity;
            });
            $tax = round($subtotal * 0.1, 2);
            $total = round($subtotal + $tax, 2);
        @endphp

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <strong>Total del pedido</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Impuesto (10%):</strong>
                        <span>${{ number_format($tax, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <strong>Total:</strong>
                        <span class="text-warning">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Artículos del pedido -->
    <div class="card">
        <div class="card-header bg-warning">
            <strong>Artículos del pedido</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $item->product) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold text-warning">
                                    ${{ number_format($item->unit_price * $item->quantity, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← Volver a los pedidos
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-warning">
            Seguir comprando
        </a>
    </div>
</div>
@endsection
