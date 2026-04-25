@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">❤️ Mis favoritos</h1>

    @if($favorites->count() > 0)
        <div class="row">
            @foreach($favorites as $favorite)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-warning">
                        <div class="card-header bg-warning text-dark">
                            <strong>{{ $favorite->product->deepestCategories()->pluck('name')->implode(', ') ?: 'Sin categorias' }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <span class="favorite-emoji">{{ $favorite->product->dinosaur_emoji ?? '🦕' }}</span>
                            </div>
                            <h5 class="card-title">{{ $favorite->product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($favorite->product->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-info">Stock: {{ $favorite->product->stock }}</span>
                                <span class="h5 text-warning mb-0">${{ number_format($favorite->product->price, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('products.show', $favorite->product) }}" class="btn btn-sm btn-info w-100 mb-2">
                                Ver producto
                            </a>
                            <form action="{{ route('favorites.remove', $favorite->product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    Quitar de favoritos
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <h4>Aún no tienes favoritos</h4>
            <p class="mb-3">¡Agrega algunos productos a tus favoritos!</p>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">
                🦕 Explorar productos
            </a>
        </div>
    @endif
</div>
@endsection
