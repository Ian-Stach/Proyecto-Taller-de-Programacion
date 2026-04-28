
<header class="navbar navbar-dark bg-black header-tall align-items-center">
    <div class="container-fluid d-flex align-items-center gap-3">

        
        <div class="d-flex gap-3 flex-shrink-0">
            
            <img src="<?php echo e(asset('images/jp_logo.jpg')); ?>"
                 alt="logo"
                 width="60"
                 height="40"
                 class="d-inline-block align-text-top"
            >

            
            <a class="navbar-brand navbar-brand-custom navbar-brand-large"
               href="<?php echo e(route('home')); ?>"
            >Jurassic Store</a>
        </div>

        
        <form method="GET"
              action="<?php echo e(route('products.index')); ?>"
              class="header-search-form"
              data-header-search-form
              data-suggestions-url="<?php echo e(route('products.suggestions')); ?>"
              role="search"
        >
            <div class="input-group header-search-group">
                <input class="form-control header-search-input"
                       type="search"
                       name="search"
                       placeholder="Buscar productos..."
                       value="<?php echo e(request('search')); ?>"
                       aria-label="Buscar productos"
                       autocomplete="off"
                >
                
                <button class="btn header-search-button"
                        type="submit"
                        aria-label="Buscar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         height="20px"
                         viewBox="0 -960 960 960"
                         width="20px"
                         fill="#000000"
                    >
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                </button>
            </div>
        </form>

        
        <ul class="nav ms-auto align-items-center flex-shrink-0">

            
            <?php if(auth()->guard()->check()): ?>
                
                <li class="nav-item">
                    <a class="nav-link scale-effect-icon header-utility-icon"
                       href="<?php echo e(route('favorites.index')); ?>"
                    >
                        
                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                        >
                            <path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Z"
                                  fill="white"
                                  class="heart-path"
                            />
                        </svg>
                    </a>
                </li>

                
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon btn btn-link header-utility-icon"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#carritoSidebar"
                            aria-controls="carritoSidebar"
                    >
                        
                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#ffffff"
                        >
                            <path d="M223.5-103.5Q200-127 200-160t23.5-56.5Q247-240 280-240t56.5 23.5Q360-193 360-160t-23.5 56.5Q313-80 280-80t-56.5-23.5Zm400 0Q600-127 600-160t23.5-56.5Q647-240 680-240t56.5 23.5Q760-193 760-160t-23.5 56.5Q713-80 680-80t-56.5-23.5ZM246-720l96 200h280l110-200H246Zm-38-80h590q23 0 35 20.5t1 41.5L692-482q-11 20-29.5 31T622-440H324l-44 80h480v80H280q-45 0-68-39.5t-2-78.5l54-98-144-304H40v-80h130l38 80Zm134 280h280-280Z"/>
                        </svg>
                    </button>
                </li>
            <?php endif; ?>

            
            <?php if(auth()->guard()->check()): ?>
                
                <li class="nav-item">
                    <a class="nav-link user-account-link text-decoration-none"
                       href="<?php echo e(route('user')); ?>"
                       aria-label="Abrir mi cuenta"
                    >
                        <span class="user-account-summary">
                            <span class="user-account-meta">
                                <span class="user-account-name"><?php echo e(Auth::user()->name); ?></span>
                                <span class="user-account-email"><?php echo e(Auth::user()->email); ?></span>
                            </span>
                            
                            <span class="user-account-avatar">
                                <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                            </span>
                        </span>
                    </a>
                </li>
            <?php else: ?>
                
                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"
                    >
                        
                        <svg xmlns="http://www.w3.org/2000/svg"
                             height="24px"
                             viewBox="0 -960 960 960"
                             width="24px"
                             fill="#ffffff"
                        >
                            <path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z"/>
                        </svg>
                        Iniciar sesión
                    </button>
                </li>

                
                <span class="nav-link">|</span>

                <li class="nav-item">
                    <button class="nav-link scale-effect-icon border-0 bg-transparent"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#registerModal"
                    >Registrarse</button>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</header><?php /**PATH C:\Users\ianiv\Herd\iniciativa-dinosaurios\resources\views\layouts\partials\header.blade.php ENDPATH**/ ?>