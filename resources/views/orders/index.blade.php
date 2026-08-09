@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">📦 Mis pedidos</h1>

    @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Artículos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        @php
                            $orderSubtotal = $order->orderItems->sum(function ($item) {
                                return $item->unit_price * $item->quantity;
                            });
                            $orderTotal = round($orderSubtotal * 1.1, 2);
                        @endphp
                        <tr>
                            <td>
                                <strong>#{{ $order->id }}</strong>
                            </td>
                            <td>
                                {{ $order->date->translatedFormat('d M Y H:i') }}
                            </td>
                            <td>
                                {{ $order->orderItems->count() }} artículo(s)
                            </td>
                            <td class="fw-bold text-warning">
                                ${{ number_format($orderTotal, 2) }}
                            </td>
                            <td>
                                @if($order->status === 'completed')
                                    <span class="badge bg-success">✅ Completado</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                                @else
                                    <span class="badge bg-danger">❌ Cancelado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                    Ver detalles
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <h4>Aún no tienes pedidos</h4>
            <p class="mb-3">Aún no has realizado ningún pedido</p>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">
                🦕 Empezar a comprar
            </a>
        </div>
    @endif
</div>
@endsection
