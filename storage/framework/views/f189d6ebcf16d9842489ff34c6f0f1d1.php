


<?php $__env->startSection('content'); ?>
<div class="principal-hero">
    <h1 class="principal-title">Bienvenido a JURASSIC STORE</h1>
    <p class="principal-subtitle">Comercializamos dinosaurios reales, listos para exhibicion, investigacion y manejo especializado.</p>

    
    <?php if(isset($featuredCategory) && isset($featuredProducts) && $featuredProducts->isNotEmpty()): ?>
        <section class="principal-featured"
                 aria-label="Carrusel de dinosaurios destacados"
                 data-principal-carousel
        >
            <div class="principal-featured-head">
                
                <h2 class="principal-featured-title">Mas disponibles ahora: <?php echo e($featuredCategory->name); ?></h2>
                <div class="principal-carousel-controls">
                    
                    <button type="button" class="principal-carousel-btn"
                            data-carousel-prev aria-label="Mostrar dinosaurios anteriores"
                    >&lsaquo;</button>
                    <button type="button" class="principal-carousel-btn"
                            data-carousel-next aria-label="Mostrar mas dinosaurios"
                    >&rsaquo;</button>
                </div>
            </div>

            
            <div class="principal-carousel-track" data-carousel-track>
                <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="principal-dino-card">
                        
                        <a href="<?php echo e(route('products.show', $product)); ?>"
                           class="principal-dino-link"
                        >
                            <div class="principal-dino-image-wrap">
                                
                                <?php if($product->image): ?>
                                    <img src="<?php echo e($product->image); ?>"
                                         alt="<?php echo e($product->name); ?>"
                                         class="principal-dino-image"
                                    >
                                <?php else: ?>
                                    <div class="principal-dino-image principal-dino-image-placeholder">Sin imagen</div>
                                <?php endif; ?>
                            </div>
                            <div class="principal-dino-body">
                                <h3 class="principal-dino-name"><?php echo e($product->name); ?></h3>
                                
                                <p class="principal-dino-price">$<?php echo e(number_format((float) $product->price, 2)); ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    
    <a href="<?php echo e(route('products.index')); ?>"
       class="principal-cta"
    >Ver dinosaurios disponibles</a>
</div>


<?php if(isset($featuredProducts) && $featuredProducts->isNotEmpty()): ?>
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
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\principal.blade.php ENDPATH**/ ?>