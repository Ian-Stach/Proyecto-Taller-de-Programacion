@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">🛒 Shopping Cart</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if(count($cartItems) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['product']->name }}</strong>
                            </td>
                            <td>${{ number_format($item['product']->price, 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td class="fw-bold text-warning">${{ number_format($item['subtotal'], 2) }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumen del Carrito -->
        <div class="row mt-4">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Subtotal:</strong>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tax (10%):</strong>
                            <span>${{ number_format($total * 0.10, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <strong>Total:</strong>
                            <span class="text-warning">${{ number_format($total * 1.10, 2) }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('checkout.store') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-lg w-100">
                        💳 Proceed to Checkout
                    </button>
                </form>

                <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <h4>Your cart is empty</h4>
            <p class="mb-3">Start shopping to add items to your cart!</p>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">
                🦕 Shop Now
            </a>
        </div>
    @endif
</div>
@endsection
