{{--
    PARTIAL: main-nav
    ─────────────────────────────────────────────────────────────────────────────
    Barra de navegación amarilla que aparece debajo del header en todas las páginas.
    Contiene los enlaces principales del sitio, distribuidos de forma equidistante.

    Clases Bootstrap relevantes:
      navbar-expand-lg  → en pantallas grandes (≥992px) muestra los links en línea;
                          en pantallas menores se puede colapsar con un botón hamburguesa
                          (en esta implementación no hay botón hamburguesa, los links
                          se mantienen visibles en todos los tamaños mediante flex).
      navbar-dark       → hace que los nav-link sean blancos (contraste sobre amarillo).
      bg-warning        → fondo amarillo de Bootstrap.
      navbar-short      → altura reducida personalizada definida en estilos.css,
                          más compacta que el header negro.
      justify-content-evenly → distribuye los enlaces con espacio igual entre ellos.
      w-100             → el contenedor de links ocupa todo el ancho disponible.

    Cada enlace usa route() para generar URLs desde nombres de ruta en routes/web.php,
    lo que hace que si alguna ruta cambia su URL, el nav se actualice automáticamente.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<nav class="navbar navbar-expand-lg navbar-dark bg-warning navbar-short">
    <div class="container-fluid">
        <div class="d-flex gap-3 align-items-center justify-content-center justify-content-evenly w-100">
            {{-- Página de inicio (hero + carrusel de categoría destacada) --}}
            <a class="nav-link"
               href="{{ route('home') }}"
            >Inicio</a>

            {{-- Catálogo completo de productos con filtros --}}
            <a class="nav-link"
               href="{{ route('products.index') }}"
            >Productos</a>

            {{-- Página informativa sobre la empresa --}}
            <a class="nav-link"
               href="{{ route('about') }}"
            >Sobre nosotros</a>

            {{-- Información sobre métodos de envío y comercialización --}}
            <a class="nav-link"
               href="{{ route('shipping') }}"
            >Comercialización</a>

            {{-- Formulario de contacto --}}
            <a class="nav-link"
               href="{{ route('contact') }}"
            >Contacto</a>

            {{-- Términos y condiciones del sitio --}}
            <a class="nav-link"
               href="{{ route('terms') }}"
            >Términos</a>
        </div>
    </div>
</nav>