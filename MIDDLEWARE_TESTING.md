# 🧪 Testing Middleware - Verificación Manual

## Cómo validar que el middleware funciona correctamente

### Test 1: Middleware `guest` en Login
**Objetivo:** Si está logueado, no puede acceder a /login

1. Abre navegador → http://iniciativa-dinosaurios.test
2. Haz click en **Login**
3. Ingresa: test@example.com / password
4. Verás: Dashboard ✅
5. Ahora intenta ir a: http://iniciativa-dinosaurios.test/login
6. **Esperado:** Redirige a /dashboard (NO muestra login) ✅

---

### Test 2: Middleware `guest` en Register
**Objetivo:** Si está logueado, no puede acceder a /register

1. Estando logueado, intenta: http://iniciativa-dinosaurios.test/register
2. **Esperado:** Redirige a /dashboard ✅

---

### Test 3: Middleware `auth` en Dashboard
**Objetivo:** Si NO está logueado, acceso denegado a dashboard

1. **Logout:** http://iniciativa-dinosaurios.test/logout
2. Intenta ir a: http://iniciativa-dinosaurios.test/dashboard
3. **Esperado:** Redirige a /login ✅

---

### Test 4: Middleware `auth` en Profile
**Objetivo:** Si NO está logueado, acceso denegado a profile

1. (Sin login) Intenta: http://iniciativa-dinosaurios.test/profile
2. **Esperado:** Redirige a /login ✅

---

### Test 5: Ruta pública sin middleware
**Objetivo:** Todos pueden acceder a Principal

1. Cierra sesión (logout)
2. Intenta: http://iniciativa-dinosaurios.test/
3. **Esperado:** Ve Principal.blade.php ✅
4. Loguéate nuevamente
5. Intenta: http://iniciativa-dinosaurios.test/
6. **Esperado:** Sigue viendo Principal (sin redirigir) ✅

---

### Test 6: Redireccionamiento automático
**Objetivo:** Después de login, redirige a intended route

1. Sin login → Intenta: http://iniciativa-dinosaurios.test/dashboard
2. Automáticamente redirige a: http://iniciativa-dinosaurios.test/login
3. Ingresa credenciales
4. **Esperado:** Redirige a /dashboard (la ruta que intentaste) ✅

---

## Resultados Esperados

Si todos los tests pasan ✅, el middleware está correctamente configurado:

| Test | Resultado |
|------|-----------|
| Test 1: Login bloqueada (logueado) | PASS |
| Test 2: Register bloqueada (logueado) | PASS |
| Test 3: Dashboard bloqueada (sin login) | PASS |
| Test 4: Profile bloqueada (sin login) | PASS |
| Test 5: Principal pública | PASS |
| Test 6: Redirección inteligente | PASS |

---

## Si algo falla

### Problema: No redirige después de login
**Solución:** Caché corrupta
```bash
php artisan cache:clear
php artisan view:clear
```

### Problema: Middleware no se aplica
**Solución:** Routes caché corruta
```bash
php artisan route:clear
```

### Problema: Error 403 Forbidden
**Solución:** Middleware `verified` activado prematuramente
- Verifica que `/dashboard` no tenga `verified`
- Solo `/orders` y `/checkout` necesitan `verified`

---

## Verificación Rápida (Terminal)

```bash
# Ver todas las rutas con middleware:
php artisan route:list
```

Busca en la salida:
- Rutas con `web` → Middleware público
- Rutas con `auth` → Middleware protegidas
- Rutas con `verified` → Protegidas + email requerido
