{{--
    VISTA: products/show (Detalle de producto)
    ─────────────────────────────────────────────────────────────────────────────
    Muestra toda la información de un producto individual: imagen, nombre,
    categorías, descripción, precio, atributos facetables, stock, acciones
    (favorito / añadir al carrito) y productos relacionados al final.

    Variables inyectadas por ProductController@show:
      $product         → Instancia de Product (cargada por Route Model Binding)
      $relatedProducts → Colección de productos de la misma categoría, excluyendo
                         el actual, limitada a 4 resultados.

    Comportamiento según estado de autenticación:
      Auth      → muestra el botón de favorito y el formulario de cantidad + carrito.
      Guest     → muestra un botón "Iniciar sesión para comprar" que abre el modal de login.
      Sin stock → muestra alerta de "Sin stock" en lugar de cualquier acción de compra.
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.Jurassic_Store')

@section('content')
@php
    /*
     * $isFavorite: determina si el producto ya está en favoritos del usuario.
     * Se consulta aquí (no en el controlador) para mantener el controlador limpio.
     * Auth::check() antes de llamar al usuario evita el error "Call to member
     * function favorites() on null" cuando el visitante es un guest.
     */
    $isFavorite = Auth::check()
        ? Auth::user()->favorites()->where('product_id', $product->id)->exists()
        : false;

    /*
     * Convierte los valores de BD (claves cortas) a etiquetas legibles para humanos.
     * Estrategia: busca en las constantes del modelo (HABITAT_OPTIONS, etc.).
     *   - Si la clave existe en la constante → usa la etiqueta definida ahí.
     *   - Si no existe (dato inesperado en BD) → capitaliza la clave con ucfirst().
     *   - Si la columna es null → la variable queda en null y no se renderiza.
     * Ejemplo: 'carnivoro' → 'Carnívoro' (según DIET_OPTIONS del modelo)
     */
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
        {{-- COLUMNA IZQUIERDA: imagen del producto --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body p-0">
                    @if($product->image)
                        <div class="products-image-wrapper product-detail-image-wrapper rounded overflow-hidden">
                            {{--
                                     onerror: fallback JS puro si la URL de imagen está rota.
                                     Oculta el <img> y muestra el placeholder hermano sin
                                     necesitar una segunda request al servidor.
                                 --}}
                            <img src="{{ asset($product->image) }}" class="card-img-top product-detail-image" alt="{{ $product->name }}" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                            <div class="bg-light products-image-placeholder product-detail-image-placeholder d-none rounded">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        </div>
                    @else
                        {{-- image es nullable en BD: se muestra el placeholder directamente --}}
                        <div class="bg-light products-image-placeholder product-detail-image-placeholder rounded">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: información, acciones y atributos --}}
        <div class="col-md-6">
            <div class="product-detail-header mb-2">
                <h1 class="mb-0">{{ $product->name }}</h1>

                {{--
                    BOTÓN DE FAVORITO (solo usuarios autenticados)
                    El formulario cambia dinámicamente de acción y método según $isFavorite:
                      - No favorito → POST a favorites.add
                      - Ya favorito → DELETE a favorites.remove (mediante @method('DELETE'))
                    La clase CSS 'is-active' cambia el color del icono SVG (corazón relleno).
                    El SVG usa fill="currentColor" para heredar el color del botón por CSS.
                --}}
                @auth
                    <form action="{{ $isFavorite ? route('favorites.remove', $product) : route('favorites.add', $product) }}" method="POST" class="product-detail-fav-form">
                        @csrf

                        @if($isFavorite)
                            @method('DELETE')
                        @endif

                        <button type="submit" class="product-detail-fav-btn {{ $isFavorite ? 'is-active' : '' }}" aria-label="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}" title="{{ $isFavorite ? 'Quitar de favoritos' : 'Agregar a favoritos' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="currentColor">
                                <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"/>
                            </svg>
                        </button>
                    </form>
                @endauth
            </div>
            
            {{--
                CATEGORÍAS (badges amarillos)
                deepestCategories() devuelve solo las categorías hoja (sin hijos)
                del árbol de categorías del producto, evitando mostrar categorías
                padre redundantes cuando el producto ya tiene subcategorías.
                @forelse muestra "Sin categoria" si la colección está vacía.
            --}}
            <div class="mb-3 d-flex flex-wrap gap-2">
                @forelse($product->deepestCategories() as $category)
                    <span class="badge bg-warning text-dark fs-6">{{ $category->name }}</span>
                @empty
                    <span class="badge bg-secondary fs-6">Sin categoria</span>
                @endforelse
            </div>

            <p class="lead">{{ $product->description }}</p>

            {{-- number_format sin (float): el cast 'decimal:2' del modelo ya garantiza 2 decimales --}}
            <h3 class="text-warning mb-3">${{ number_format($product->price, 2) }}</h3>

            {{--
                ATRIBUTOS FACETABLES
                Cada atributo solo se renderiza si tiene valor (no es null).
                Las etiquetas ($habitatLabel, $dietLabel, $eraLabel) se prepararon
                en el bloque @php con fallback a ucfirst() para datos inesperados.
                (float) en height_meters: el cast 'decimal:2' devuelve string;
                number_format necesita float para formatear correctamente.
            --}}
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

            {{--
                BADGE DE STOCK con tres estados de color:
                  > 5 unidades → verde (bg-success): disponible con holgura
                  1-5 unidades → amarillo (bg-warning): stock bajo
                  0 unidades   → rojo (bg-danger): sin stock
            --}}
            <div class="mb-4">
                <strong>Stock:</strong>
                <span class="badge @if($product->stock > 5) bg-success @elseif($product->stock > 0) bg-warning text-dark @else bg-danger @endif">{{ $product->stock }} unidades</span>
            </div>

            {{--
                ACCIONES DE COMPRA según combinación de stock × autenticación:

                stock > 0 + @auth  → formulario de cantidad + botón "Añadir al carrito"
                    max="$product->stock" en el input number → el navegador bloquea
                    cantidades superiores al stock antes de enviar el formulario.
                    El controlador también valida esto server-side (doble capa).

                stock > 0 + @guest → botón que abre el modal de login (no hay ruta
                    de compra para guests, login es un modal no una página).

                stock = 0          → alerta roja "Sin stock"; sin formulario ni botón.
            --}}
            @if($product->stock > 0)
                @auth
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4 cart-add-form">
                        @csrf

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad:</label>
                            {{-- max limita el input en el cliente; el servidor también valida --}}
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control product-quantity-input">
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg me-2">🛒 Añadir al carrito
                        </button>
                    </form>
                @else
                    {{-- Guest: el botón abre el modal de login (no redirige a una página) --}}
                    <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar sesión para comprar</button>
                @endauth
            @else
                <div class="alert alert-danger">Sin stock</div>
            @endif

            <hr>

            <a href="{{ route('products.index') }}" class="btn btn-secondary">← Volver a productos</a>
        </div>
    </div>

    {{--
        PRODUCTOS RELACIONADOS
        Cargados por el controlador: misma categoría que $product, excluyendo
        el producto actual, limitados a 4, solo activos.
        Solo se renderiza la sección si hay al menos un relacionado.
        Las tarjetas muestran solo nombre, precio y enlace al detalle
        (versión simplificada respecto a las cards del catálogo).
    --}}
    @if($relatedProducts->count() > 0)
        <div class="row mt-5 pt-4 border-top">
            <div class="col-md-12">
                <h3>Productos relacionados</h3>
            </div>
            @foreach($relatedProducts as $related)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="fs-5 fw-bold text-warning mt-auto">${{ number_format($related->price, 2) }}</p>
                            <a class="btn btn-sm btn-info w-100" href="{{ route('products.show', $related) }}">Detalles</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
