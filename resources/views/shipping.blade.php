{{--
    VISTA: shipping (Comercialización)
    ─────────────────────────────────────────────────────────────────────────────
    Página estática de información comercial. Sin variables inyectadas;
    se sirve directamente con Route::view('/shipping', 'shipping').

    Estructura de secciones:
      1. Tipos de Entregas     → tres cards (Estándar / Expresa / VIP)
      2. Zonas de Cobertura    → tres list-groups informativos (sin enlaces)
      3. Formas de Envío      → dos cards en altura uniforme (h-100)
      4. Formas de Pago        → lista de métodos + alerta de seguridad
      5. Política de Devoluciones → card con condiciones y proceso
      6. CTA                   → botones a Contacto y Catálogo
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-4 mb-4 static-page-title">📦 Comercialización</h1>
            <p class="lead">Información completa sobre entregas, envíos y formas de pago</p>
            <hr class="mb-5">
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 1: Tipos de Entregas
         Tres opciones de envío en cards de igual altura (h-100).
         Cada card muestra tiempo, costo, descripción y características
         incluidas. border-warning + card-header bg-warning mantiene
         la paleta amarilla del sitio.
    ========================================================= --}}
    <!-- Tipos de Entregas -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">🚚 Tipos de Entregas</h2>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>📍 Entrega Estándar</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tiempo:</strong> 7-10 días hábiles</p>
                    <p><strong>Costo:</strong> $8,000 USD</p>
                    <p class="text-muted">
                        Envío aéreo en contenedor climatizado. Incluye aseguramiento básico y seguimiento en tiempo real.
                    </p>
                    <p class="small">✓ Transporte seguro<br>✓ Climatización<br>✓ Seguimiento GPS</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>⚡ Entrega Expressa</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tiempo:</strong> 3-5 días hábiles</p>
                    <p><strong>Costo:</strong> $25,000 USD</p>
                    <p class="text-muted">
                        Vuelo privado exclusivo con veterinario acompañante. Máxima rapidez y cuidado especializado.
                    </p>
                    <p class="small">✓ Veterinario incluido<br>✓ Vuelo privado exclusivo<br>✓ Cuidado premium</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5>🏆 Entrega VIP</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tiempo:</strong> 1-2 días hábiles</p>
                    <p><strong>Costo:</strong> $75,000 USD</p>
                    <p class="text-muted">
                        Servicio personalizado con equipo especializado. Incluye instalación en sitio y capacitación completa.
                    </p>
                    <p class="small">✓ Equipo especializado<br>✓ Instalación<br>✓ Entrenamiento</p>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 2: Zonas de Cobertura
         Tres list-groups informativos (no son enlaces; se usan <div>
         en lugar de <a> para evitar el comportamiento de enlace y
         el anuncio incorrecto en lectores de pantalla).
         El primer ítem de cada grupo actúa como título de región
         con bg-warning + fw-bold. border-primary aporta el borde azul
         a todo el grupo.
    ========================================================= --}}
    <!-- Zonas de Cobertura -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">🌍 Zonas de Cobertura</h2>
        </div>

        <div class="col-md-4">
            <div class="list-group border border-primary">
                <div class="list-group-item bg-warning text-dark fw-bold">
                    🌎 América del Norte
                </div>
                <div class="list-group-item">
                    Estados Unidos (todos los estados)
                </div>
                <div class="list-group-item">
                    Canadá (todas las provincias)
                </div>
                <div class="list-group-item">
                    México (todo el país)
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="list-group border border-primary">
                <div class="list-group-item bg-warning text-dark fw-bold">
                    🌍 América Latina
                </div>
                <div class="list-group-item">
                    Argentina, Brasil, Chile, Colombia
                </div>
                <div class="list-group-item">
                    Perú, Venezuela, Uruguay, Paraguay
                </div>
                <div class="list-group-item">
                    Otros países bajo solicitud especial
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="list-group border border-primary">
                <div class="list-group-item bg-warning text-dark fw-bold">
                    🌏 Europa, Asia, Oceanía
                </div>
                <div class="list-group-item">
                    Unión Europea (todos los países)
                </div>
                <div class="list-group-item">
                    Reino Unido, Asia (principal)
                </div>
                <div class="list-group-item">
                    Australia, Nueva Zelanda, Japón
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 3: Formas de Envío
         Dos cards con h-100 para garantizar igual altura en la fila.
         card-body p-0 + list-group-flush elimina el padding del body
         y los bordes laterales de la lista, integrando los ítems
         directamente con el borde de la card.
    ========================================================= --}}
    <!-- Formas de Envío -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">✈️ Formas de Envío</h2>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Transporte Especializado</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Transporte Aéreo:</strong> Aviones de carga adaptados con sistemas de climatización y oxígeno
                        </li>
                        <li class="list-group-item">
                            <strong>Contenedores Bioseguros:</strong> Diseñados especialmente para dinosaurios, con control de temperatura, humedad y presión
                        </li>
                        <li class="list-group-item">
                            <strong>Monitoreo 24/7:</strong> Sensores IoT que envían datos cada 15 minutos
                        </li>
                        <li class="list-group-item">
                            <strong>Seguro Total:</strong> Cobertura de accidentes, daños y pérdida total
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Proceso de Envío</h5>
                </div>
                <div class="card-body p-0">
                    <ol class="list-group list-group-numbered list-group-flush">
                        <li class="list-group-item">Confirmación de entrega y detalles del dinosaurio</li>
                        <li class="list-group-item">Preparación especializada (3-5 días)</li>
                        <li class="list-group-item">Embalaje en contenedor bioseguro</li>
                        <li class="list-group-item">Transporte al aeropuerto</li>
                        <li class="list-group-item">Vuelo a destino</li>
                        <li class="list-group-item">Entrega con certificados y documentación</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 4: Formas de Pago
         Columna izquierda: lista de métodos aceptados.
         Columna derecha: alert alert-info con información de seguridad
         (SSL, PCI DSS, anti-fraude y facturación).
    ========================================================= --}}
    <!-- Formas de Pago -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">💳 Formas de Pago</h2>
        </div>

        <div class="col-md-6 mb-4">
            <h5>Métodos de Pago Disponibles</h5>
            <ul class="list-group">
                <li class="list-group-item">💳 Tarjetas de Crédito (Visa, Mastercard, Amex)</li>
                <li class="list-group-item">🏦 Transferencia Bancaria Internacional</li>
                <li class="list-group-item">💰 Criptomonedas (Bitcoin, Ethereum)</li>
                <li class="list-group-item">📱 Billeteras Digitales (Apple Pay, Google Pay)</li>
                <li class="list-group-item">✅ Financiamiento en Cuotas (12, 24, 36 meses)</li>
            </ul>
        </div>

        <div class="col-md-6 mb-4">
            <h5>Información de Seguridad</h5>
            <div class="alert alert-info">
                <p><strong>🔒 Seguridad SSL:</strong> Todos los pagos están encriptados con protocolo SSL de 256 bits</p>
                <p><strong>✓ PCI Compliant:</strong> Nuestro sistema cumple con los estándares PCI DSS</p>
                <p><strong>🛡️ Protección Fraud:</strong> Sistema anti-fraude avanzado</p>
                <p class="mb-0"><strong>📋 Facturación:</strong> Se emite factura al momento de la compra</p>
            </div>
        </div>
    </div>

    {{-- =========================================================
         SECCIÓN 5: Política de Devoluciones
         Card con borde warning que detalla período, condiciones,
         porcentaje de reembolso y pasos del proceso de devolución.
    ========================================================= --}}
    <!-- Políticas de Devolución -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">↩️ Política de Devoluciones</h2>
            <div class="card border-warning">
                <div class="card-body">
                    <p>
                        <strong>Período de devolución:</strong> 30 días desde la entrega
                    </p>
                    <p>
                        <strong>Condiciones:</strong> El dinosaurio debe estar en perfecto estado de salud y no haber sufrido daños por negligencia del propietario
                    </p>
                    <p>
                        <strong>Reembolso:</strong> 100% del monto pagado por el dinosaurio (no incluye gastos de envío de ida)
                    </p>
                    <p class="mb-0">
                        <strong>Proceso:</strong> Contacta a nuestro equipo, realizaremos una inspección veterinaria, y procesaremos la devolución en 5-7 días hábiles
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA: acceso rápido a Contacto y al catálogo de productos --}}
    <div class="mt-5 text-center">
        <a href="{{ route('contact') }}" class="btn btn-warning btn-lg me-2">
            📞 Contactar para más información
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg">
            Ver catálogo
        </a>
    </div>
</div>
@endsection
