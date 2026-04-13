@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">🦕 All Products</h1>

    <!-- Filtros de Categoría -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <a href="{{ route('products.index') }}" class="btn btn-outline-warning">All</a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->name]) }}" 
                       class="btn btn-outline-warning">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tarjetas de Productos -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 50) }}</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark">{{ $product->category->name }}</span>
                            <span class="badge 
                                @if($product->stock > 5) bg-success 
                                @elseif($product->stock > 0) bg-warning text-dark
                                @else bg-danger @endif">
                                Stock: {{ $product->stock }}
                            </span>
                        </div>

                        <p class="fs-5 fw-bold text-warning">${{ number_format($product->price, 2) }}</p>

                        <div class="d-flex gap-2">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info flex-grow-1">
                                👁️ View
                            </a>
                            @if(Auth::check())
                                <form action="{{ route('favorites.add', $product) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">❤️</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No products found in this category.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="row mt-5">
        <div class="col-md-12">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
