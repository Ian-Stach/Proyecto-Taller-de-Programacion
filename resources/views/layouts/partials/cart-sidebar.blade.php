{{--
    PARTIAL: cart-sidebar
    ─────────────────────────────────────────────────────────────────────────────
    Panel lateral deslizante (offcanvas) que muestra el contenido del carrito.
    Solo se incluye en el layout para usuarios autenticados (@auth en el layout).

    Variables requeridas (inyectadas por el ViewComposer del carrito):
      $sidebarCartItems    → array de items, cada uno con:
                               'product'  → modelo Product con name, price, etc.
                               'quantity' → cantidad de unidades del producto.
                               'subtotal' → precio × cantidad (calculado en el composer).
      $sidebarCartSubtotal → suma de todos los subtotales, sin impuesto.

    Offcanvas de Bootstrap:
      offcanvas-end  → el panel se desliza desde el borde derecho de la pantalla.
      tabindex="-1"  → excluye el contenedor del orden de tabulación normal;
                        Bootstrap lo gestiona cuando el panel está abierto.
      El botón en el header con data-bs-target="#carritoSidebar" activa este panel.
    ─────────────────────────────────────────────────────────────────────────────
--}}
<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="carritoSidebar"
     aria-labelledby="carritoSidebarLabel"
>

    {{-- Cabecera del panel: título y botón de cierre --}}
    <div class="offcanvas-header bg-warning">
        <h5 class="offcanvas-title"
            id="carritoSidebarLabel"
        >🛒 Carrito de compras</h5>
        {{-- data-bs-dismiss="offcanvas" cierra el panel sin JavaScript propio --}}
        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Cerrar carrito"
        ></button>
    </div>

    {{--
        Cuerpo del panel: flex column para que los botones del pie queden
        siempre pegados al fondo independientemente de la cantidad de items.
    --}}
    <div class="offcanvas-body d-flex flex-column">

        {{--
            ZONA DE ITEMS: flex-grow-1 hace que ocupe todo el espacio disponible,
            empujando los botones de acción hacia el fondo del panel.
        --}}
        <div class="cart-items flex-grow-1">
            @if(count($sidebarCartItems) > 0)
                {{-- list-group-flush elimina los bordes y bordes redondeados del grupo --}}
                <div class="list-group list-group-flush">
                    @foreach($sidebarCartItems as $item)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    {{-- Nombre del producto accedido desde el modelo relacionado --}}
                                    <h6 class="mb-1">{{ $item['product']->name }}</h6>
                                    <small class="text-muted">Cant.: {{ $item['quantity'] }}</small>
                                </div>

                                {{-- Columna derecha: precio arriba, botón eliminar abajo --}}
                                <div class="d-flex flex-column align-items-end gap-1">
                                    {{--
                                        Subtotal del item: precio × cantidad.
                                        number_format(value, 2) garantiza siempre dos decimales.
                                    --}}
                                    <span class="text-warning fw-bold">${{ number_format($item['subtotal'], 2) }}</span>

                                    {{--
                                        Botón eliminar con ícono de papelera.
                                        cart-remove-form → interceptado por cart.js para
                                        eliminar el producto sin recargar la página.
                                    --}}
                                    <form action="{{ route('cart.remove', $item['product']) }}"
                                          method="POST"
                                          class="cart-remove-form"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-link p-0 border-0"
                                                aria-label="Eliminar {{ $item['product']->name }} del carrito"
                                                title="Eliminar"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 height="24px"
                                                 viewBox="0 -960 960 960"
                                                 width="24px"
                                                 fill="#818181"
                                            >
                                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr class="my-3">

                {{-- RESUMEN DE TOTALES --}}
                <div class="mb-3">
                    {{-- Subtotal: suma de todos los items sin impuesto --}}
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span>${{ number_format($sidebarCartSubtotal, 2) }}</span>
                    </div>

                    {{-- Impuesto fijo del 10% calculado en la vista (no en el modelo) --}}
                    <div class="d-flex justify-content-between">
                        <strong>Impuesto (10%):</strong>
                        <span>${{ number_format($sidebarCartSubtotal * 0.1, 2) }}</span>
                    </div>

                    {{-- Total final = subtotal + 10% de impuesto --}}
                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fs-6 fw-bold">
                        <strong>Total:</strong>
                        <span class="text-warning">${{ number_format($sidebarCartSubtotal * 1.1, 2) }}</span>
                    </div>
                </div>
            @else
                {{-- Estado vacío: mensaje centrado cuando no hay items en el carrito --}}
                <p class="text-muted text-center py-5">Tu carrito está vacío</p>
            @endif
        </div>

        {{--
            BOTONES DE ACCIÓN: mt-auto los empuja siempre al fondo del panel
            gracias al d-flex flex-column del offcanvas-body.
        --}}
        <div class="mt-auto d-flex flex-column gap-2">
            {{-- "Ver carrito" solo aparece si hay items; no tiene sentido mostrarlo vacío --}}
            @if(count($sidebarCartItems) > 0)
                <a href="{{ route('cart.show') }}"
                   class="btn btn-warning w-100"
                >Ver carrito</a>
            @endif

            {{--
                "Seguir comprando" siempre visible.
                data-bs-dismiss="offcanvas" cierra el panel al hacer clic,
                devolviendo el foco a la página del catálogo.
            --}}
            <a href="{{ route('products.index') }}"
               class="btn btn-secondary w-100"
               data-bs-dismiss="offcanvas"
            >Seguir comprando</a>
        </div>
    </div>
</div>