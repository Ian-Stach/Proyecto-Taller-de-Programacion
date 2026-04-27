@extends('layouts.Jurassic_Store')

@section('content')
@php
    $isFavorite = Auth::check()
        ? Auth::user()->favorites()->where('product_id', $product->id)->exists()
        : false;

    $habitatLabel = $product->habitat !== null
        ? (\App\Models\Product::HABITAT_OPTIONS[$product->habitat] ?? ucfirst($product->habitat))
        : null;

    $dietLabel = $product->diet !== null
        ? (\App\Models\Product::DIET_OPTIONS[$product->diet] ?? ucfirst($product->diet))
        : null;

    $eraLabel = $product->era !== null
        ? (\App\Models\Product::ERA_OPTIONS[$product->era] ?? ucfirst($product->era))
        : null;
@endphp

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
            <div class="product-detail-header mb-2">
                <h1 class="mb-0">{{ $product->name }}</h1>

                @auth
                    <form action="{{ $isFavorite ? route('favorites.remove', $product) : route('favorites.add', $product) }}"
                          method="POST"
                          class="product-detail-fav-form"
                    >
                        @csrf

                        @if($isFavorite)
                            @method('DELETE')
                        @endif

                        <button type="submit"
                                class="product-detail-fav-btn {{ $isFavorite ? 'is-active' : '' }}"
                                aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                                title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="currentColor">
                                <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
                            </svg>
                        </button>
                    </form>
                @endauth
            </div>
            
            <div class="mb-3 d-flex flex-wrap gap-2">
                @forelse($product->deepestCategories() as $category)
                    <span class="badge bg-warning text-dark fs-6">{{ $category->name }}</span>
                @empty
                    <span class="badge bg-secondary fs-6">Sin categoria</span>
                @endforelse
            </div>

            <p class="lead">{{ $product->description }}</p>

            <h3 class="text-warning mb-3">${{ number_format($product->price, 2) }}</h3>

            <div class="mb-3 product-detail-attributes">
                @if($product->height_meters !== null)
                    <div class="product-detail-attribute-item">
                        <strong>Altura:</strong>
                        <span class="badge bg-info text-dark">{{ number_format((float) $product->height_meters, 2) }} m</span>
                    </div>
                @endif

                @if($habitatLabel)
                    <div class="product-detail-attribute-item">
                        <strong>Hábitat:</strong>
                        <span class="badge bg-primary">{{ $habitatLabel }}</span>
                    </div>
                @endif

                @if($dietLabel)
                    <div class="product-detail-attribute-item">
                        <strong>Dieta:</strong>
                        <span class="badge bg-success">{{ $dietLabel }}</span>
                    </div>
                @endif

                @if($eraLabel)
                    <div class="product-detail-attribute-item">
                        <strong>Era:</strong>
                        <span class="badge bg-secondary">{{ $eraLabel }}</span>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <strong>Stock:</strong>
                <span class="badge @if($product->stock > 5) bg-success @elseif($product->stock > 0) bg-warning text-dark @else bg-danger @endif">
                    {{ $product->stock }} unidades
                </span>
            </div>

            @if($product->stock > 0)
                @auth
                    <form action="{{ route('cart.add', $product) }}"
                          method="POST" class="mb-4"
                    >
                        @csrf
                    
                        <div class="mb-3">
                            <label for="quantity"
                                   class="form-label"
                            >Cantidad:
                            </label>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   value="1"
                                   min="1"
                                   max="{{ $product->stock }}"
                                   class="form-control product-quantity-input"
                            >
                        </div>

                        <button type="submit"
                                class="btn btn-warning btn-lg me-2"
                        >🛒 Añadir al carrito
                        </button>
                    </form>
                     @else
                        <button type="button"
                                class="btn btn-warning btn-lg"
                                data-bs-toggle="modal"
                                data-bs-target="#loginModal"
                        >Iniciar sesión para comprar</button>
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
