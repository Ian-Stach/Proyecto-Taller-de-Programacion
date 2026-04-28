{{--
    VISTA: products/index (Catálogo de productos)
    ─────────────────────────────────────────────────────────────────────────────
    Vista contenedor del catálogo. Hereda el layout principal y delega
    toda la lógica de renderizado a dos sub-vistas parciales:

      products.partials.catalog-content  → estructura de dos columnas:
                                            sidebar de filtros (izquierda)
                                            + resultados con paginación (derecha)

    Variables inyectadas por ProductController@index:
      $products       → LengthAwarePaginator con los productos filtrados
      $filterFacets   → array de grupos de filtros (categorías + atributos)
      $currentSort    → valor del sort activo (ej: 'price_asc')

    data-products-page → selector JS usado por products-index.js para inicializar
                         la lógica AJAX de filtrado/paginación sin recargar la página.

    products-index.js → gestiona el submit asíncrono del formulario de filtros,
                        actualiza el bloque de resultados con la respuesta parcial
                        del servidor, y sincroniza la URL con pushState.
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.Jurassic_Store')

@section('content')
{{-- data-products-page: punto de montaje para el JS de filtrado asíncrono --}}
<div class="container-fluid my-5" data-products-page>
    {{--
        Delega el renderizado completo a catalog-content:
          → sidebar de facetas + columna de resultados con sort, chips y cards.
        Separar en un partial permite que el controlador devuelva solo este
        fragmento en respuestas AJAX (sin layout, sin nav, sin footer).
    --}}
    @include('products.partials.catalog-content')
</div>

{{--
    Script de filtrado AJAX del catálogo.
    defer: se descarga en paralelo pero ejecuta después de parsear el DOM,
    garantizando que data-products-page ya existe cuando el JS lo busca.
--}}
<script src="{{ asset('js/products-index.js') }}" defer></script>
@endsection
