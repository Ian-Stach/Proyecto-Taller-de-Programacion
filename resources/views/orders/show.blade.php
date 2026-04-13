@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>📦 Order #{{ $order->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            @if($order->status === 'completed')
                <span class="badge bg-success fs-5">✅ Completed</span>
            @elseif($order->status === 'pending')
                <span class="badge bg-warning text-dark fs-5">⏳ Pending</span>
            @else
                <span class="badge bg-danger fs-5">❌ Cancelled</span>
            @endif
        </div>
    </div>

    <!-- Información de la Orden -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Order Information</strong>
                </div>
                <div class="card-body">
                    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>Customer:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <strong>Order Total</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span>${{ number_format($order->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Tax (10%):</strong>
                        <span>${{ number_format($order->total_price * 0.10, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold">
                        <strong>Total:</strong>
                        <span class="text-warning">${{ number_format($order->total_price * 1.10, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items de la Orden -->
    <div class="card">
        <div class="card-header bg-warning">
            <strong>Order Items</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
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
            ← Back to Orders
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-warning">
            Continue Shopping
        </a>
    </div>
</div>
@endsection
