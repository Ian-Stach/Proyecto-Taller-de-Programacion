<ul class="nav flex-column gap-2">
    <li class="nav-item">
        <a class="nav-link sidebar-account-link <?php echo e($currentPanel === 'overview' ? 'is-current' : ''); ?>"
           href="<?php echo e(route('user', ['panel' => 'overview'])); ?>"
           <?php if($currentPanel === 'overview'): ?> aria-current="page" <?php endif; ?>
        ><span class="me-2">🏠</span> Información general</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link <?php echo e($currentPanel === 'security' ? 'is-current' : ''); ?>"
           href="<?php echo e(route('user', ['panel' => 'security'])); ?>"
           <?php if($currentPanel === 'security'): ?> aria-current="page" <?php endif; ?>
        ><span class="me-2">🔒</span> Seguridad</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link <?php echo e($currentPanel === 'orders' ? 'is-current' : ''); ?>"
           href="<?php echo e(route('user', ['panel' => 'orders'])); ?>"
           <?php if($currentPanel === 'orders'): ?> aria-current="page" <?php endif; ?>
        ><span class="me-2">🧾</span> Pedidos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link <?php echo e($currentPanel === 'favorites' ? 'is-current' : ''); ?>"
           href="<?php echo e(route('user', ['panel' => 'favorites'])); ?>"
           <?php if($currentPanel === 'favorites'): ?> aria-current="page" <?php endif; ?>
        ><span class="me-2">⭐</span> Favoritos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link <?php echo e($currentPanel === 'edit' ? 'is-current' : ''); ?>"
           href="<?php echo e(route('user', ['panel' => 'edit'])); ?>"
           <?php if($currentPanel === 'edit'): ?> aria-current="page" <?php endif; ?>
        ><span class="me-2">✏️</span> Editar perfil</a>
    </li>
</ul>
<?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/profile/partials/account-nav-links.blade.php ENDPATH**/ ?>