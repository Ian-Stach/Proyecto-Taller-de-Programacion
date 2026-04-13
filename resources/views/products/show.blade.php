@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Detalles del Producto -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body bg-light p-5">
                    <div class="bg-warning p-5 text-center rounded">
                        <h2>🦖 {{ $product->name }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            
            <div class="mb-3">
                <span class="badge bg-warning text-dark fs-6">{{ $product->category->name }}</span>
            </div>

            <p class="lead">{{ $product->description }}</p>

            <h3 class="text-warning mb-3">${{ number_format($product->price, 2) }}</h3>

            <div class="mb-4">
                <strong>Stock Disponible:</strong>
                <span class="badge @if($product->stock > 5) bg-success @elseif($product->stock > 0) bg-warning text-dark @else bg-danger @endif">
                    {{ $product->stock }} unidades
                </span>
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control" style="width: 100px;">
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg me-2">
                        🛒 Add to Cart
                    </button>

                    @if(Auth::check())
                        <form action="{{ route('favorites.add', $product) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg">❤️ Add to Favorites</button>
                        </form>
                    @endif
                </form>
            @else
                <div class="alert alert-danger">
                    Out of Stock
                </div>
            @endif

            <hr>

            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                ← Back to Products
            </a>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5 pt-4 border-top">
            <div class="col-md-12">
                <h3>Related Products</h3>
            </div>
            @foreach($relatedProducts as $related)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="fs-5 fw-bold text-warning">${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('products.show', $related) }}" class="btn btn-sm btn-info w-100">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
