{{--
    VISTA: favorites/index
    ─────────────────────────────────────────────────────────────────────────────
    Lista de productos marcados como favoritos por el usuario autenticado.
    Extiende el layout principal del sitio (con header, nav, carrito y footer).

    Variables inyectadas por FavoriteController@index:
      $favorites → LengthAwarePaginator de registros Favorite con eager loading
                   'product.categories' (evita N+1 al renderizar categorías e imagen).
                   Paginado a 12 por página.

    Estructura:
      · Si hay favoritos → grilla de tarjetas (col-md-4) con imagen, categoría,
        nombre, descripción recortada, stock, precio y acciones (ver / quitar).
      · Si no hay favoritos → alerta informativa con enlace al catálogo.
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">❤️ Mis favoritos</h1>

    @if($favorites->count() > 0)
        <div class="row">
            @foreach($favorites as $favorite)
                {{--
                    $product se asigna una vez al inicio del loop para evitar
                    acceder a $favorite->product repetidamente en cada referencia.
                --}}
                @php $product = $favorite->product; @endphp
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-warning">
                        {{--
                            Cabecera de la tarjeta: muestra las categorías más específicas
                            del producto usando deepestCategories(), que filtra las categorías
                            ancestro y devuelve solo las hojas. No hace queries extra gracias
                            al eager loading 'product.categories' del controlador.
                        --}}
                        <div class="card-header bg-warning text-dark">
                            <strong>{{ $product->deepestCategories()->pluck('name')->implode(', ') ?: 'Sin categorias' }}</strong>
                        </div>
                        <div class="card-body">
                            {{--
                                Bloque de imagen con altura fija (product-card-image-wrap, definida
                                en estilos.css: 180px, overflow hidden, fondo gris). Garantiza que
                                todas las tarjetas ocupen el mismo espacio visual independientemente
                                de si el producto tiene imagen o no.
                                Si la imagen existe pero su URL falla al cargar, onerror la oculta
                                y activa el placeholder "Sin imagen".
                            --}}
                            <div class="product-card-image-wrap mb-3">
                                @if($product->image ?? false)
                                    <img src="{{ $product->image }}"
                                         class="product-card-img"
                                         alt="{{ $product->name }}"
                                         onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');"
                                    >
                                    <div class="product-card-img-placeholder d-none">
                                        <span class="text-muted">Sin imagen</span>
                                    </div>
                                @else
                                    <div class="product-card-img-placeholder">
                                        <span class="text-muted">Sin imagen</span>
                                    </div>
                                @endif
                            </div>
                            <h5 class="card-title">{{ $product->name }}</h5>
                            {{-- Descripción recortada a 100 caracteres para mantener altura uniforme en las tarjetas --}}
                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="badge bg-info">Stock: {{ $product->stock }}</span>
                                <span class="h5 text-warning mb-0">${{ number_format($product->price, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 pt-0">
                            {{-- Enlace a la ficha completa del producto --}}
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info w-100 mb-2">
                                Ver producto
                            </a>
                            {{-- Formulario DELETE a favorites.remove para quitar el producto de favoritos --}}
                            <form action="{{ route('favorites.remove', $product) }}" method="POST">
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

        {{-- Paginación: links generados por Laravel con el paginador por defecto --}}
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @else
        {{-- Estado vacío: se muestra cuando el usuario no tiene ningún favorito guardado --}}
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
