

<?php $__env->startSection('content'); ?>
<div class="principal-hero">
    <form method="GET" action="<?php echo e(route('products.index')); ?>" class="principal-search py-3">
        <div class="input-group principal-search-group">
            <input class="form-control principal-search-input"
                   type="search"
                   name="search"
                   value="<?php echo e(request('search')); ?>"
                   placeholder="Buscar productos..."
                   aria-label="Buscar productos">
                <button class="btn d-flex align-items-center justify-content-center principal-search-button"
                        type="submit"
                        aria-label="Enviar búsqueda">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                </button>
        </div>
    </form>

    <h1 class="principal-title">Bienvenido a JURASSIC STORE</h1>
    <p class="principal-subtitle">Aquí puedes encontrar los mejores juguetes y productos de dinosaurios.</p>
    <a href="<?php echo e(route('products.index')); ?>" class="principal-cta">Ver productos</a>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/principal.blade.php ENDPATH**/ ?>