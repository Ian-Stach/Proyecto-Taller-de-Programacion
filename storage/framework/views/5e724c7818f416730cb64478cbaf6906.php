

<?php $__env->startSection('content'); ?>

<div style="background-image: url('<?php echo e(asset('images/Principal_bg.png')); ?>'); background-size: cover; background-position: center top; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.7); margin: 0; padding: 0; padding-top: 40px;">
    <!-- BARRA DE BÚSQUEDA -->
    <div class="py-3" style="width: 100%; display: flex; justify-content: center;">
        <div class="input-group" style="width: 100%; max-width: 500px; border-radius: 25px; overflow: hidden;">
            <input class="form-control" type="search" placeholder="Search products..." aria-label="Search" style="border-radius: 25px 0 0 25px; border: 1px solid #000000;">
            <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="border-radius: 0 25px 25px 0; border: 1px solid #000000; background: #ff8080; padding: 8px 16px;" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                    <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                </svg>
            </button>
        </div>
    </div>

    <h1 style="margin-top: 100px;">Welcome to JURASSIC STORE</h1>
    <p>Here you can find the best dinosaur toys and merchandise!</p>
    <a href="/products">View Products</a>
</div>

<?php $__env->stopSection(); ?>


        <!-- VIDEO JULY3P
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#videoModal">
            YO NO ME COMI NINGUN TRAVA
        </button>
        <div class="modal fade" id="videoModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">IDOLO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <video width="100%" controls>
                            <source src="<?php echo e(asset('videos/yo_no_me_comi_ningun_trava.mp4')); ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div> -->
<?php echo $__env->make('layouts.Jurassic_Store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views/Principal.blade.php ENDPATH**/ ?>