@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">📦 My Orders</h1>

    @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>#{{ $order->id }}</strong>
                            </td>
                            <td>
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </td>
                            <td>
                                {{ $order->orderItems->count() }} item(s)
                            </td>
                            <td class="fw-bold text-warning">
                                ${{ number_format($order->total_price, 2) }}
                            </td>
                            <td>
                                @if($order->status === 'completed')
                                    <span class="badge bg-success">✅ Completed</span>
                                @elseif($order->status === 'pending')
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                @else
                                    <span class="badge bg-danger">❌ Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                    View Details
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
            <h4>No orders yet</h4>
            <p class="mb-3">You haven't placed any orders</p>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">
                🦕 Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
