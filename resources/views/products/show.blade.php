@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Detalles del Producto -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body p-0">
                    @if($product->image)
                        <div class="products-image-wrapper product-detail-image-wrapper rounded overflow-hidden">
                            <img src="{{ $product->image }}"
                                 class="card-img-top product-detail-image"
                                 alt="{{ $product->name }}"
                                 onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="bg-light products-image-placeholder product-detail-image-placeholder d-none rounded">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        </div>
                    @else
                        <div class="bg-light products-image-placeholder product-detail-image-placeholder rounded">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            
            <div class="mb-3 d-flex flex-wrap gap-2">
                @forelse($product->deepestCategories() as $category)
                    <span class="badge bg-warning text-dark fs-6">{{ $category->name }}</span>
                @empty
                    <span class="badge bg-secondary fs-6">Sin categoria</span>
                @endforelse
            </div>

            <p class="lead">{{ $product->description }}</p>

            <h3 class="text-warning mb-3">${{ number_format($product->price, 2) }}</h3>

            @if($product->height_meters !== null)
                <div class="mb-3">
                    <strong>Altura:</strong>
                    <span class="badge bg-info text-dark">{{ number_format((float) $product->height_meters, 2) }} m</span>
                </div>
            @endif

            <div class="mb-4">
                <strong>Stock:</strong>
                <span class="badge @if($product->stock > 5) bg-success @elseif($product->stock > 0) bg-warning text-dark @else bg-danger @endif">
                    {{ $product->stock }} unidades
                </span>
            </div>

            @if($product->stock > 0)
                @auth
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4">
                        @csrf
                    
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control product-quantity-input">
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg me-2">
                            🛒 Añadir al carrito
                        </button>
                    </form>
                    <form action="{{ route('favorites.add', $product) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">❤️ Añadir a favoritos</button>
                    </form>
                     @else
                        <div class="alert alert-info">
                            Inicia sesión para agregar este producto al carrito.
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-warning btn-lg">Iniciar sesión para comprar</a>
                @endauth
            @else
                <div class="alert alert-danger">
                    Sin stock
                </div>
            @endif

            <hr>

            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                ← Volver a productos
            </a>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5 pt-4 border-top">
            <div class="col-md-12">
                <h3>Productos relacionados</h3>
            </div>
            @foreach($relatedProducts as $related)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="fs-5 fw-bold text-warning">${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('products.show', $related) }}" class="btn btn-sm btn-info w-100">Detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
