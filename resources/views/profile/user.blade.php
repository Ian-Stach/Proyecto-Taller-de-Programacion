<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>JURASSIC STORE</title>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    </head>

    <body>
        <!-- HEADER PARA EL USER -->
         <header class="navbar navbar-expand-lg navbar-dark bg-black navbar-tall align-items-center">
            <div class="container-fluid">
                <div class="d-flex gap-3">
                    <img src="{{ asset('images/jp_logo.jpg') }}" alt="logo" width="60" height="40" class="d-inline-block align-text-top">
                    <a class="navbar-brand navbar-brand-custom" href="/Principal" style="font-size: 2rem;">Jurassic Store</a>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-1">
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="My profile">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#opcion_1">
                                My profile
                            </button>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-2">
                <div id="opcion_1" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <h3>My profile</h3>
                        <p>Name: {{ Auth::user()->name }}</p>
                        <p>Email: {{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>



        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>

