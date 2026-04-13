# 🦖 JURASSIC_STORE.BLADE.PHP - GUÍA DE USO

## ¿Qué cambió?

El layout `Jurassic_Store.blade.php` ahora es **dinámico y listo para autenticación**:

### Antes:
```html
<!-- Siempre mostraba Login/Register -->
<a href="/login">Login</a>
<a href="/register">Register</a>
```

### Ahora:
```blade
@auth
    <!-- Si está logueado: muestra nombre + logout -->
    👤 Usuario: {{ Auth::user()->name }}
    🚪 Logout
@else
    <!-- Si NO está logueado: muestra login/register -->
    <a href="/login">Login</a>
    <a href="/register">Register</a>
@endauth
```

---

## Estructura del Layout Actualizado

```
Jurassic_Store.blade.php
│
├── HEADER (Negro)
│   ├── Logo + Nombre tienda
│   ├── Icono Favoritos
│   ├── Icono Carrito
│   └── Auth Dinámico:
│       ├── Si logueado: Nombre + Logout (amarillo)
│       └── Si no logueado: Login/Register (blanco)
│
├── NAVBAR (Naranja)
│   ├── Home
│   └── Categories (dropdown)
│
├── @yield('content')  ← Aquí va el contenido específico
│
├── SIDEBAR Carrito (Offcanvas)
│
└── FOOTER (Negro)
```

---

## Cómo Usarlo en Vistas

### Ejemplo 1: Página de Productos

**Archivo:** `resources/views/products/index.blade.php`

```blade
@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container my-5">
        <h1>🦕 All Products</h1>
        
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>{{ $product->name }}</h5>
                            <p>${{ $product->price }}</p>
                            <button class="btn btn-warning">Add to Cart</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No products found.</p>
            @endforelse
        </div>
    </div>
@endsection
```

---

### Ejemplo 2: Detalle de Producto

**Archivo:** `resources/views/products/show.blade.php`

```blade
@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <h1>{{ $product->name }}</h1>
                <p>{{ $product->description }}</p>
                <p class="fs-4">Precio: <strong>${{ $product->price }}</strong></p>
                <p>Stock: {{ $product->stock }} unidades</p>
                
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                    <button type="submit" class="btn btn-warning btn-lg">🛒 Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
@endsection
```

---

### Ejemplo 3: Carrito

**Archivo:** `resources/views/cart/show.blade.php`

```blade
@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container my-5">
        <h1>🛒 Shopping Cart</h1>
        
        @if(count($cartItems) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>${{ $item->product->price }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ $item->product->price * $item->quantity }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <a href="{{ route('checkout') }}" class="btn btn-warning btn-lg">Proceed to Checkout</a>
        @else
            <p>Your cart is empty</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
        @endif
    </div>
@endsection
```

---

### Ejemplo 4: Mis Órdenes (Protegida)

**Archivo:** `resources/views/orders/index.blade.php`

```blade
@extends('layouts.Jurassic_Store')

@section('content')
    <div class="container my-5">
        <h1>👤 My Orders</h1>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(Auth::user()->orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>${{ $order->total_price }}</td>
                        <td>
                            <span class="badge 
                                @if($order->status === 'completed') bg-success 
                                @elseif($order->status === 'pending') bg-warning 
                                @else bg-danger @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
```

---

## Características Especiales

### 1. **Autenticación Dinámica**
- Cambio automático de header según si el usuario está logueado
- Botón Logout solo visible para autenticados
- Links Login/Register solo para no autenticados

### 2. **Navbar de Categorías**
- Dropdown funcional para filtrar por categorías
- Links a `/products`, `/products?category=action-figures`, etc.

### 3. **Sidebar Carrito**
- Offcanvas (panel deslizable desde la derecha)
- Botones "Checkout" y "Continue Shopping"
- Se abre al hacer click en el icono 🛒

### 4. **Bootstrap + CSS Personalizado**
- Usa Bootstrap 5 para responsive design
- CSS personalizado en `public/css/estilos.css`
- Efectos hover en iconos (clase `scale-effect-icon`)

### 5. **Footer Consistente**
- About section
- Links útiles
- Social media ready
- Copyright

---

## Próximos Pasos (PASO 4)

Usa este layout para crear las siguientes vistas:

1. `products/index.blade.php` → GET /products
2. `products/show.blade.php` → GET /products/{id}
3. `cart/show.blade.php` → GET /cart
4. `orders/index.blade.php` → GET /orders (protegida)
5. `orders/show.blade.php` → GET /orders/{id} (protegida)

**Todas ellas heredarán del layout Jurassic_Store.blade.php**

---

## Verificación

**Para verificar que funciona:**

1. **Sin login:** Ve a `/Principal` y verás:
   - Links "Login" y "Register" en la esquina derecha

2. **Con login:** Loguéate en `/login` y ve a una ruta que use este layout:
   - Verás tu nombre en amarillo: `👤 Tu Nombre`
   - Verás botón rojo "🚪 Logout"
   - Los links Login/Register desaparecen

---

## Variables Disponibles en las Vistas

```blade
@auth
    {{ Auth::user()->name }}      ← Nombre del usuario
    {{ Auth::user()->email }}     ← Email del usuario
    {{ Auth::user()->id }}        ← ID del usuario
@endauth
```

---

✅ **Layout listo para producción con tiendas de e-commerce**
