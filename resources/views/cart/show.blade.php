{{--
    VISTA: cart/show
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------
    Página completa del carrito de compras del usuario autenticado.
    Extiende el layout principal del sitio (con header, nav, carrito y footer).

    El carrito vive en la sesión PHP como array [ product_id => quantity ].
    No hay tabla ni modelo Cart en la base de datos.

    Variables inyectadas por CartController@index:
      $cartItems  → array de arrays con claves 'product', 'quantity' y 'subtotal'. Si el carrito está vacío, es un array vacío [].
      $total      → suma de todos los subtotales (sin impuesto).
      $tax        → impuesto del 10% sobre $total, calculado en el controlador.
      $grandTotal → $total + $tax, calculado en el controlador.

    Estructura:
      · Si hay ítems → tabla con productos, cantidades y subtotales + resumen de pedido (subtotal, impuesto, total) + botón de checkout y enlace para seguir comprando.
      · Si el carrito está vacío → alerta informativa con enlace al catálogo.
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------
--}}

@extends('layouts.Jurassic_Store')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">🛒 Carrito de compras</h1>

    <!-- Errores de validación (ej: cantidad supera el stock al añadir un producto) -->
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if(count($cartItems) > 0)
        <!-- Tabla de productos en el carrito -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['product']->name }}</strong>
                            </td>
                            <td>${{ number_format($item['product']->price, 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td class="fw-bold text-warning">${{ number_format($item['subtotal'], 2) }}</td>
                            <td>
                                <!-- Formulario DELETE a cart.remove para quitar el producto del carrito -->
                                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumen del Carrito -->
        <div class="row mt-4">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Resumen del Pedido</h5>
                        <!-- $total, $tax y $grandTotal vienen calculados desde el controlador -->
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Subtotal:</strong>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Impuesto (10%):</strong>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <strong>Total:</strong>
                            <span class="text-warning">${{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Formulario de checkout: inicia el proceso de pago -->
                <form action="{{ route('checkout.store') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-lg w-100">Proceder al pago</button>
                </form>

                <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2">Seguir comprando</a>
            </div>
        </div>
    @else
        <!-- Estado vacío: se muestra cuando el carrito no tiene ningún producto -->
        <div class="alert alert-info text-center py-5">
            <h4>Tu carrito está vacío</h4>
            <p class="mb-3">¡Comienza a comprar para agregar artículos a tu carrito!</p>
            <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg">Comprar Ahora</a>
        </div>
    @endif
</div>
@endsection