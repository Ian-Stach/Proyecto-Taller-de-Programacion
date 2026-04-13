# 🔐 Middleware Configuration - Iniciativa Dinosaurios

## Resumen de Middlewares

### 1. **guest** - Solo usuarios SIN login
Redirige a `/dashboard` si usuario intenta acceder
```
Ejemplo: Si logueado intenta ir a /login → va a /dashboard
```

### 2. **auth** - Solo usuarios CON login  
Redirige a `/login` si usuario intenta acceder
```
Ejemplo: Si no logueado intenta ir a /dashboard → va a /login
```

### 3. **verified** - Solo usuarios CON login + email verificado
Redirige a `/email/verify` si email NO está verificado
```
Ejemplo: Si intenta ir a /checkout sin verificar email → ve pantalla verificación
```

---

## Estructura de Rutas por Middleware

### 🟢 Rutas Públicas (Acceso a todos)
- `GET  /` → Principal page
- `GET  /Principal` → Principal page

### 🟡 Rutas Guest Only (Sin login)
- `GET  /login` → Formulario login
- `POST /login` → Procesar login
- `GET  /register` → Formulario registro
- `POST /register` → Procesar registro
- `GET  /forgot-password` → Reset contraseña
- `POST /forgot-password` → Procesar reset
- `GET  /reset-password/{token}` → Nueva contraseña
- `POST /reset-password` → Guardar nueva contraseña

### 🔵 Rutas Auth Only (Con login + sin verificación requerida)
- `GET  /dashboard` → Dashboard
- `GET  /profile` → Editar perfil
- `POST /profile` → Actualizar perfil
- `DELETE /profile` → Eliminar perfil
- `GET  /verify-email` → Pantalla verificación email
- `POST /email/verification-notification` → Reenviar email
- `GET  /confirm-password` → Confirmar contraseña
- `POST /confirm-password` → Procesar confirmación
- `PUT  /password` → Cambiar contraseña
- `POST /logout` → Logout

### 🔴 Rutas Auth + Verified (Con login + email verificado)
*Rutas a agregar en Paso 4:*
- `GET  /cart` → Ver carrito
- `POST /cart` → Agregar al carrito
- `DELETE /cart/{item}` → Eliminar del carrito
- `GET  /orders` → Mis órdenes
- `GET  /orders/{id}` → Detalle orden
- `POST /checkout` → Procesar pago

---

## Flujos de Usuario

### Flujo: Usuario NO autenticado intenta acceder a /dashboard
```
GET /dashboard (sin token)
    ↓
Middleware auth valida
    ↓
¿Tiene sesión? NO
    ↓
Redirige a /login
```

### Flujo: Usuario autenticado intenta acceder a /login
```
GET /login (con token)
    ↓
Middleware guest valida
    ↓
¿Está logueado? SÍ
    ↓
Redirige a /dashboard (intended route)
```

### Flujo: Usuario logueado intenta checkout sin verificación
```
GET /checkout (con token, email NO verificado)
    ↓
Middleware auth:verified valida
    ↓
¿Email verificado? NO
    ↓
Redirige a /email/verify
    ↓
Usuario verifica email
    ↓
Puede acceder a /checkout
```

---

## Cómo agregar middleware a nuevas rutas

### Ejemplo 1: Ruta protegida (Auth)
```php
Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});
```

### Ejemplo 2: Ruta protegida con verificación
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});
```

### Ejemplo 3: Solo guests (sin login)
```php
Route::middleware('guest')->group(function () {
    Route::get('/special-offer', [OfferController::class, 'show']);
});
```

### Ejemplo 4: Ruta pública (sin middleware)
```php
Route::get('/about', [PageController::class, 'about'])->name('about');
```

---

## Referencia Rápida

| Situación | Middleware | Efecto |
|-----------|-----------|--------|
| Solo logueados pueden entrar | `auth` | No logueado → /login |
| Solo sin login pueden entrar | `guest` | Logueado → /dashboard |
| Logueados + email verificado | `auth`, `verified` | Verificación requerida |
| Todos pueden entrar | (ninguno) | Acceso público |

---

## Ubicación de Archivos

- **Rutas públicas:** `routes/web.php` (línea 4-8)
- **Rutas autenticación:** `routes/auth.php` (completo)
- **Rutas protegidas:** `routes/web.php` (línea 15-27, will expand in Paso 4)

---

## Notas Aplicadas

✅ Línea 13 en auth.php: `Route::middleware('guest')->group(...)` protege login/register
✅ Línea 38 en auth.php: `Route::middleware('auth')->group(...)` protege email/password
✅ Línea 15 en web.php: `Route::middleware('auth')->group(...)` protege dashboard

En Paso 4 agregaremos `verified` para rutas sensibles de órdenes/checkout.
