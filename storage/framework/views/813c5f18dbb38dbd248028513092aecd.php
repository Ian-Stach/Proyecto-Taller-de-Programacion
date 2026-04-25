<?php if($paginator->hasPages()): ?>
    <?php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $pageLinks = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
            ->filter(fn (int $page) => $page >= 1 && $page <= $paginator->lastPage())
            ->unique()
            ->sort()
            ->values();

        $previousRenderedPage = null;
    ?>

    <nav role="navigation" aria-label="Pagination Navigation" class="products-pagination-nav">
        <ul class="products-pagination-list">
            <?php if($paginator->onFirstPage()): ?>
                <li class="products-pagination-item" aria-disabled="true">
                    <span class="products-pagination-link is-arrow is-disabled" aria-label="Pagina anterior">&lt;</span>
                </li>
            <?php else: ?>
                <li class="products-pagination-item">
                    <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="products-pagination-link is-arrow" aria-label="Pagina anterior">&lt;</a>
                </li>
            <?php endif; ?>

            <?php $__currentLoopData = $pageLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($previousRenderedPage !== null && $page - $previousRenderedPage > 1): ?>
                    <li class="products-pagination-item" aria-disabled="true">
                        <span class="products-pagination-link is-disabled is-ellipsis">...</span>
                    </li>
                <?php endif; ?>

                <?php if($page === $paginator->currentPage()): ?>
                    <li class="products-pagination-item" aria-current="page">
                        <span class="products-pagination-link is-current"><?php echo e($page); ?></span>
                    </li>
                <?php else: ?>
                    <li class="products-pagination-item">
                        <a href="<?php echo e($paginator->url($page)); ?>" class="products-pagination-link"><?php echo e($page); ?></a>
                    </li>
                <?php endif; ?>

                <?php
                    $previousRenderedPage = $page;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($paginator->hasMorePages()): ?>
                <li class="products-pagination-item">
                    <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="products-pagination-link is-arrow" aria-label="Pagina siguiente">&gt;</a>
                </li>
            <?php else: ?>
                <li class="products-pagination-item" aria-disabled="true">
                    <span class="products-pagination-link is-arrow is-disabled" aria-label="Pagina siguiente">&gt;</span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>