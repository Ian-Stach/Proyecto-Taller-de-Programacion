{{--
    VISTA: principal (Home / Página de inicio)
    ─────────────────────────────────────────────────────────────────────────────
    Es la página de bienvenida del sitio. Hereda el layout principal y define
    su contenido en el slot @section('content').

    Variables inyectadas por el controlador (HomeController o similar):
      $featuredCategory  → Instancia de Category: la categoría con más productos
                           activos, usada como título del carrusel.
      $featuredProducts  → Colección de Product: los productos de esa categoría,
                           limitados a un número razonable para el carrusel.

    Ambas variables se verifican con isset() + isNotEmpty() antes de renderizar
    el carrusel porque podrían no existir si la BD está vacía (ej: BD recién creada).

    Estructura de la vista:
      1. Hero           → título, subtítulo y CTA al catálogo.
      2. Carrusel       → productos destacados en slider horizontal (solo si hay datos).
      3. Script inline  → lógica del carrusel en JS (solo si hay productos).
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.Jurassic_Store')

@section('content')
<div class="principal-hero">
    <h1 class="principal-title">Bienvenido a JURASSIC STORE</h1>
    <p class="principal-subtitle">Comercializamos dinosaurios reales, listos para exhibicion, investigacion y manejo especializado.</p>

    {{--
        SECCIÓN CARRUSEL DE DESTACADOS
        Solo se renderiza si el controlador pudo determinar una categoría destacada
        y hay al menos un producto en ella.
        data-principal-carousel → selector usado por el script JS para inicializar el carrusel.
        aria-label             → accesibilidad: describe la sección para lectores de pantalla.
    --}}
    @if(isset($featuredCategory) && isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <section class="principal-featured"
                 aria-label="Carrusel de dinosaurios destacados"
                 data-principal-carousel
        >
            <div class="principal-featured-head">
                {{-- El nombre de la categoría se muestra dinámicamente como título del carrusel --}}
                <h2 class="principal-featured-title">Mas disponibles ahora: {{ $featuredCategory->name }}</h2>
                <div class="principal-carousel-controls">
                    {{--
                        Botones de navegación del carrusel.
                        data-carousel-prev / data-carousel-next → el JS los selecciona para
                        adjuntar los listeners de click. No usan ids para evitar conflictos
                        si el componente se reutiliza en otra parte de la página.
                        &lsaquo; / &rsaquo; → entidades HTML para los chevrons < y >
                    --}}
                    <button type="button" class="principal-carousel-btn"
                            data-carousel-prev aria-label="Mostrar dinosaurios anteriores"
                    >&lsaquo;</button>
                    <button type="button" class="principal-carousel-btn"
                            data-carousel-next aria-label="Mostrar mas dinosaurios"
                    >&rsaquo;</button>
                </div>
            </div>

            {{--
                PISTA DEL CARRUSEL
                Contenedor con overflow-x: auto (definido en CSS).
                data-carousel-track → el JS lo selecciona para aplicar scrollBy().
                Cada tarjeta es un <article> con enlace al detalle del producto.
            --}}
            <div class="principal-carousel-track" data-carousel-track>
                @foreach($featuredProducts as $product)
                    <article class="principal-dino-card">
                        {{--
                            route('products.show', $product) → Route Model Binding:
                            Laravel toma el $product->id automáticamente para construir la URL.
                        --}}
                        <a href="{{ route('products.show', $product) }}"
                           class="principal-dino-link"
                        >
                            <div class="principal-dino-image-wrap">
                                {{--
                                    image es nullable en BD: si no tiene foto, muestra un placeholder.
                                    El alt usa el nombre del producto para accesibilidad e SEO.
                                --}}
                                @if($product->image)
                                    <img src="{{ $product->image }}"
                                         alt="{{ $product->name }}"
                                         class="principal-dino-image"
                                    >
                                @else
                                    <div class="principal-dino-image principal-dino-image-placeholder">Sin imagen</div>
                                @endif
                            </div>
                            <div class="principal-dino-body">
                                <h3 class="principal-dino-name">{{ $product->name }}</h3>
                                {{--
                                    (float) convierte el string con decimales que devuelve el cast
                                    'decimal:2' del modelo a un float nativo de PHP.
                                    number_format(..., 2) garantiza siempre dos decimales (ej: $1,200.00).
                                --}}
                                <p class="principal-dino-price">${{ number_format((float) $product->price, 2) }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA principal: lleva al catálogo completo de productos --}}
    <a href="{{ route('products.index') }}"
       class="principal-cta"
    >Ver dinosaurios disponibles</a>
</div>

{{--
    SCRIPT DEL CARRUSEL
    Solo se inyecta si hay productos destacados. Si la sección no se renderizó,
    el script tampoco es necesario y se evita código muerto en el HTML.

    Lógica de getStep():
      - Lee el ancho real de la primera tarjeta con getBoundingClientRect().width
        (más preciso que offsetWidth porque incluye fracciones de pixel).
      - Lee el gap entre tarjetas de los estilos computados del track.
      - cardsPerStep = min(4, total_cards): avanza de a 4 tarjetas como máximo,
        o menos si hay pocas (evita scroll excesivo en carruseles cortos).
      - Resultado: offset en píxeles = (ancho + gap) × cardsPerStep

    scrollBy({ left: ±step, behavior: 'smooth' }) → desplazamiento nativo del
    navegador con animación CSS (no requiere librería externa).
--}}
@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Busca el carrusel en el DOM; aborta si la sección no existe
            const root = document.querySelector('[data-principal-carousel]');

            if (!root) {
                return;
            }

            const track = root.querySelector('[data-carousel-track]');
            const prevButton = root.querySelector('[data-carousel-prev]');
            const nextButton = root.querySelector('[data-carousel-next]');

            // Aborta si falta algún elemento clave (robustez ante cambios de HTML)
            if (!track || !prevButton || !nextButton) {
                return;
            }

            // Calcula cuántos píxeles avanzar al presionar un botón
            function getStep() {
                const cards = track.querySelectorAll('.principal-dino-card');
                const firstCard = cards[0];

                // Fallback de 280px si el carrusel está vacío o no ha sido pintado aún
                if (!firstCard) {
                    return 280;
                }

                const styles = window.getComputedStyle(track);
                // columnGap o gap (según navegador); si no está definido, asume 16px
                const gap = Number.parseFloat(styles.columnGap || styles.gap || '16');
                // Avanza de a 4 tarjetas como máximo; menos si el carrusel tiene pocas
                const cardsPerStep = Math.min(4, cards.length);

                // NaN puede ocurrir si el gap es 'normal'; el fallback a 16 evita NaN × n
                return (firstCard.getBoundingClientRect().width + (Number.isNaN(gap) ? 16 : gap)) * cardsPerStep;
            }

            // Desplazamiento suave hacia la izquierda (productos anteriores)
            prevButton.addEventListener('click', function () {
                track.scrollBy({ left: -getStep(), behavior: 'smooth' });
            });

            // Desplazamiento suave hacia la derecha (más productos)
            nextButton.addEventListener('click', function () {
                track.scrollBy({ left: getStep(), behavior: 'smooth' });
            });
        });
    </script>
@endif

@endsection