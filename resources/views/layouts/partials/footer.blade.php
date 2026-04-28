{{--
    PARTIAL: footer
    ─────────────────────────────────────────────────────────────────────────────
    Pie de página común a todas las vistas del sitio.
    Dividido en tres columnas con el grid de Bootstrap (col-md-4 × 3 = 12 columnas).
    En pantallas menores a md (768px) las columnas se apilan verticalmente.

    Estructura:
      Columna 1 → descripción breve de la empresa.
      Columna 2 → enlaces rápidos a secciones del sitio.
      Columna 3 → llamado a seguir en redes sociales.
      Pie final  → línea separadora + copyright con año fijo.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<footer class="bg-black text-white py-4">
    <div class="container-fluid">
        <div class="row">

            {{-- COLUMNA 1: descripción de la empresa --}}
            <div class="col-md-4">
                <h5>Sobre nosotros</h5>
                <p>Jurassic Store trae la emoción prehistórica a la vida con nuestra exclusiva colección de dinosaurios.</p>
            </div>

            {{-- COLUMNA 2: enlaces rápidos --}}
            <div class="col-md-4">
                <h5>Enlaces</h5>
                <ul class="list-unstyled">
                    {{-- text-white-50 aplica color blanco al 50% de opacidad (gris suave) --}}
                    <li>
                        <a href="{{ route('about') }}"
                           class="text-white-50"
                        >Sobre nosotros</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                           class="text-white-50"
                        >Contacto</a>
                    </li>
                </ul>
            </div>

            {{-- COLUMNA 3: redes sociales (actualmente solo texto, sin links) --}}
            <div class="col-md-4">
                <h5>Síguenos</h5>
                <p class="text-white-50">¡Mantente actualizado con nuestros últimos descubrimientos de dinosaurios!</p>
            </div>
        </div>

        {{-- Línea divisora horizontal entre las columnas y el copyright --}}
        <hr>

        {{-- COPYRIGHT: año fijo 2026, texto centrado en gris suave --}}
        <div class="text-center text-white-50">
            <p>&copy; 2026 Jurassic Store. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>