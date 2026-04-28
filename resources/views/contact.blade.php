{{--
    VISTA: contact.blade.php
    Descripción: Página de contacto de Jurassic Store.
    Permite al usuario comunicarse con el equipo mediante un formulario,
    consultar información de contacto (dirección, teléfono, email),
    ver la ubicación en el mapa y revisar las preguntas frecuentes.

    Estructura:
    1. Cabecera      — Título principal y subtítulo
    2. Info contacto — Tres tarjetas: Dirección, Teléfono, Email
    3. Ubicación     — Mapa embebido de Google Maps (Isla Nublar)
    4. Formulario    — Form POST con CSRF, validación y 6 campos + checkbox
    5. FAQ           — Acordeón Bootstrap con 4 preguntas frecuentes

    Rutas relacionadas:
    - GET  /contact          → muestra esta vista (ContactController@show)
    - POST /contact          → procesa el formulario (ContactController@store)
    - GET  /terms            → enlazada desde el checkbox de T&C
--}}
@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">

    {{-- ================================================================
         SECCIÓN 1: CABECERA
         Título h1 de la página, bajada y separador visual.
    ================================================================ --}}
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-4 mb-4 static-page-title">📞 Contacto</h1>
            <p class="lead">¿Preguntas sobre nuestros dinosaurios? ¡Contáctanos!</p>
            <hr class="mb-5">
        </div>
    </div>

    {{-- ================================================================
         SECCIÓN 2: TARJETAS DE INFORMACIÓN DE CONTACTO
         Tres tarjetas con borde amarillo (border-warning) en fila.
         h-100 garantiza que todas tengan la misma altura.
         Contenidos: Dirección física, Teléfono (+1-800-DINOLAB) y Email.
    ================================================================ --}}
    <!-- Información de Contacto -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>📍 Dirección</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Jurassic Store International</strong><br>
                        Complejo Central Jurassic Store<br>
                        Bahía Suroeste, Isla Nublar<br>
                        Costa Rica
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>☎️ Teléfono</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Línea Principal:</strong><br>
                        +1-800-DINOLAB<br>
                        (1-800-346-6522)
                    </p>
                    <p class="mb-0">
                        <strong>Disponible:</strong> 24/7
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>✉️ Email</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Soporte General:</strong><br>
                        support@jurassicstore.com
                    </p>
                    <p class="mb-0">
                        <strong>Ventas:</strong><br>
                        sales@jurassicstore.com
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         SECCIÓN 3: MAPA DE UBICACIÓN
         Iframe de Google Maps embebido (sin API key).
         Apunta a Isla Nublar en el Océano Pacífico, Costa Rica.
         El card ocupa el ancho completo (col-md-12) con p-0 en card-body
         para que el mapa llegue hasta los bordes del contenedor.
    ================================================================ --}}
    <!-- Ubicación -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5>🗺️ Nuestra Ubicación — Isla Nublar, Costa Rica</h5>
                </div>
                <div class="card-body p-0">
                    <iframe src="https://maps.google.com/maps?q=5.5167,-87.0667&z=11&output=embed"
                            width="100%"
                            height="450"
                            style="border:0; display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Ubicación de Isla Nublar en el Océano Pacífico, Costa Rica"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         SECCIÓN 4: FORMULARIO DE CONTACTO
         Formulario POST con protección CSRF.
         Centrado en col-md-8 offset-md-2.
         Campos: Nombre, Email, Teléfono, Asunto (select), Mensaje, T&C.
         Muestra errores de validación del servidor (@error / $errors).
         Muestra mensaje de éxito de sesión si el envío fue exitoso.
    ================================================================ --}}
    <!-- Formulario de Contacto -->
    <div class="row mb-5">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">📝 Formulario de Contacto</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>¡Por favor revisa los siguientes errores!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre Completo *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Asunto *</label>
                    <select class="form-select @error('subject') is-invalid @enderror" 
                            id="subject" name="subject" required>
                        <option value="">Selecciona un asunto...</option>
                        <option value="consulta_general" {{ old('subject') == 'consulta_general' ? 'selected' : '' }}>Consulta General</option>
                        <option value="soporte_producto" {{ old('subject') == 'soporte_producto' ? 'selected' : '' }}>Soporte del Producto</option>
                        <option value="pedido" {{ old('subject') == 'pedido' ? 'selected' : '' }}>Pregunta sobre Pedido</option>
                        <option value="sugerencia" {{ old('subject') == 'sugerencia' ? 'selected' : '' }}>Sugerencia</option>
                        <option value="reclamo" {{ old('subject') == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                        <option value="otro" {{ old('subject') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Mensaje *</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" 
                              id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input @error('agree') is-invalid @enderror" 
                           id="agree" name="agree" required {{ old('agree') ? 'checked' : '' }}>
                    <label class="form-check-label" for="agree">
                        Acepto los <a href="{{ route('terms') }}">Términos y Condiciones</a> *
                    </label>
                    @error('agree')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning btn-lg w-100">
                    ✉️ Enviar Mensaje
                </button>
            </form>
        </div>
    </div>

    {{-- ================================================================
         SECCIÓN 5: PREGUNTAS FRECUENTES (FAQ)
         Acordeón Bootstrap (#faqAccordion) con 4 ítems.
         Todos los ítems arrancan colapsados (clase 'collapsed' en button).
         Separado del formulario con border-top, mt-5 y pt-5.
         Preguntas: tiempos de entrega, garantía, legalidad, capacitación.
    ================================================================ --}}
    <!-- FAQ -->
    <div class="row mt-5 pt-5 border-top">
        <div class="col-md-12">
            <h2 class="mb-4">❓ Preguntas Frecuentes</h2>
        </div>

        <div class="col-md-12">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Cuánto tiempo tarda en llegar mi dinosaurio?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Depende de la opción de envío que elijas. Estándar: 7-10 días, Expresa: 3-5 días, VIP: 1-2 días
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Qué incluye la garantía?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            12 meses de garantía de salud. Si el dinosaurio muere por causas no atribuibles a negligencia, ofrecemos reemplazo o reembolso
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Es legal tener un dinosaurio donde vivo?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Es tu responsabilidad verificar la legalidad en tu jurisdicción. Algunos países/estados pueden requerir permisos especiales
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Ofrecen capacitación?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí, incluimos programas de capacitación gratuitos sobre cuidado, alimentación y comportamiento del dinosaurio
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
