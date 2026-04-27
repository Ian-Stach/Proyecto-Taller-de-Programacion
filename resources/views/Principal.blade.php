@extends('layouts.Jurassic_Store')

@section('content')
<div class="principal-hero">
    <h1 class="principal-title">Bienvenido a JURASSIC STORE</h1>
    <p class="principal-subtitle">Comercializamos dinosaurios reales, listos para exhibicion, investigacion y manejo especializado.</p>

    @if(isset($featuredCategory) && isset($featuredProducts) && $featuredProducts->isNotEmpty())
        <section class="principal-featured" aria-label="Carrusel de dinosaurios destacados" data-principal-carousel>
            <div class="principal-featured-head">
                <h2 class="principal-featured-title">Mas disponibles ahora: {{ $featuredCategory->name }}</h2>
                <div class="principal-carousel-controls">
                    <button type="button" class="principal-carousel-btn" data-carousel-prev aria-label="Mostrar dinosaurios anteriores">
                        &lsaquo;
                    </button>
                    <button type="button" class="principal-carousel-btn" data-carousel-next aria-label="Mostrar mas dinosaurios">
                        &rsaquo;
                    </button>
                </div>
            </div>

            <div class="principal-carousel-track" data-carousel-track>
                @foreach($featuredProducts as $product)
                    <article class="principal-dino-card">
                        <a href="{{ route('products.show', $product) }}" class="principal-dino-link">
                            <div class="principal-dino-image-wrap">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="principal-dino-image">
                                @else
                                    <div class="principal-dino-image principal-dino-image-placeholder">Sin imagen</div>
                                @endif
                            </div>
                            <div class="principal-dino-body">
                                <h3 class="principal-dino-name">{{ $product->name }}</h3>
                                <p class="principal-dino-price">${{ number_format((float) $product->price, 2) }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <a href="{{ route('products.index') }}" class="principal-cta">Ver dinosaurios disponibles</a>
</div>

@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('[data-principal-carousel]');

            if (!root) {
                return;
            }

            const track = root.querySelector('[data-carousel-track]');
            const prevButton = root.querySelector('[data-carousel-prev]');
            const nextButton = root.querySelector('[data-carousel-next]');

            if (!track || !prevButton || !nextButton) {
                return;
            }

            function getStep() {
                const cards = track.querySelectorAll('.principal-dino-card');
                const firstCard = cards[0];

                if (!firstCard) {
                    return 280;
                }

                const styles = window.getComputedStyle(track);
                const gap = Number.parseFloat(styles.columnGap || styles.gap || '16');
                const cardsPerStep = Math.min(4, cards.length);

                return (firstCard.getBoundingClientRect().width + (Number.isNaN(gap) ? 16 : gap)) * cardsPerStep;
            }

            prevButton.addEventListener('click', function () {
                track.scrollBy({ left: -getStep(), behavior: 'smooth' });
            });

            nextButton.addEventListener('click', function () {
                track.scrollBy({ left: getStep(), behavior: 'smooth' });
            });
        });
    </script>
@endif

@endsection