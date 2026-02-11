# 🔧 Solución al Error de Sesión del Carrito

## ❌ Error Original
```
Session store not set on request
```

Este error ocurre porque las rutas API no tienen habilitadas las sesiones por defecto en Laravel.

## ✅ Cambios Realizados

### 1. Habilitado Middleware de Sanctum (`app/Http/Kernel.php`)

**Antes:**
```php
'api' => [
    // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

**Después:**
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 2. Configurado CORS (`config/cors.php`)

**Antes:**
```php
'supports_credentials' => false,
```

**Después:**
```php
'supports_credentials' => true,
```

### 3. Añadido Variables de Entorno (`.env`)

**Añadido al final del archivo:**
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8000,127.0.0.1,127.0.0.1:8000
SESSION_DOMAIN=localhost
```

### 4. Actualizado Store del Carrito (`resources/js/stores/cart.js`)

Añadida inicialización automática del token CSRF antes de cada petición:

```javascript
// Initialize CSRF token
let csrfInitialized = false;

async function initializeCsrf() {
  if (!csrfInitialized) {
    try {
      await axios.get('/sanctum/csrf-cookie');
      csrfInitialized = true;
    } catch (error) {
      console.error('Error initializing CSRF:', error);
    }
  }
}
```

Cada método del store ahora llama a `await initializeCsrf()` antes de hacer la petición.

## 🚀 Pasos para Aplicar la Solución

### 1. Limpiar Caché de Laravel
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 2. Recompilar Assets de Frontend
```bash
npm run dev
```

### 3. Reiniciar el Servidor (si está corriendo)
```bash
# Detener el servidor (Ctrl+C)
# Volver a iniciar
php artisan serve
```

### 4. Limpiar Cookies del Navegador
- Abre las DevTools (F12)
- Ve a Application > Cookies
- Elimina todas las cookies de localhost
- Recarga la página (F5)

## 🧪 Verificar que Funciona

1. Abre la consola del navegador (F12)
2. Ve a la pestaña Network
3. Intenta añadir un producto al carrito
4. Deberías ver:
   - Primera petición: `GET /sanctum/csrf-cookie` (200 OK)
   - Segunda petición: `POST /api/cart/items` (200 OK)
   - En las cookies: `XSRF-TOKEN` y `laravel_session`

## 📝 Cómo Funciona

### Flujo de Autenticación con Sanctum

1. **Primera petición**: El frontend solicita el token CSRF
   ```
   GET /sanctum/csrf-cookie
   ```

2. **Laravel responde** con cookies:
   - `XSRF-TOKEN`: Token CSRF
   - `laravel_session`: ID de sesión

3. **Peticiones subsecuentes**: Axios envía automáticamente:
   - Cookies de sesión
   - Header `X-XSRF-TOKEN`

4. **Laravel valida**:
   - Verifica el token CSRF
   - Verifica la sesión
   - Permite la petición

### Middleware EnsureFrontendRequestsAreStateful

Este middleware de Sanctum:
- Habilita sesiones para rutas API
- Verifica tokens CSRF
- Permite cookies en peticiones API
- Solo funciona para dominios en `SANCTUM_STATEFUL_DOMAINS`

## 🔍 Troubleshooting

### Error: "CSRF token mismatch"
**Solución:**
1. Limpia cookies del navegador
2. Verifica que `supports_credentials: true` en CORS
3. Verifica que Axios tenga `withCredentials: true`

### Error: "Session store not set"
**Solución:**
1. Verifica que el middleware de Sanctum esté descomentado
2. Ejecuta `php artisan config:clear`
3. Reinicia el servidor

### Las cookies no se envían
**Solución:**
1. Verifica que estés usando el mismo dominio (localhost)
2. No uses IP (127.0.0.1) si tu app está en localhost
3. Verifica `SANCTUM_STATEFUL_DOMAINS` en .env

### Error 419 (CSRF token mismatch)
**Solución:**
1. Limpia cookies del navegador
2. Verifica que la ruta `/sanctum/csrf-cookie` esté accesible
3. Verifica que Axios esté configurado correctamente

## 🎯 Configuración de Axios (Ya aplicada)

En `resources/js/bootstrap.js`:
```javascript
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
```

Esto asegura que:
- Las cookies se envíen en cada petición
- El token CSRF se incluya automáticamente

## 📚 Documentación Adicional

- [Laravel Sanctum - SPA Authentication](https://laravel.com/docs/sanctum#spa-authentication)
- [Axios - withCredentials](https://axios-http.com/docs/req_config)
- [CORS - credentials](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#credentials)

## ✅ Checklist de Verificación

- [x] Middleware de Sanctum descomentado
- [x] CORS con `supports_credentials: true`
- [x] Variables de entorno añadidas
- [x] Store actualizado con inicialización CSRF
- [x] Axios configurado con credenciales
- [ ] Caché de Laravel limpiada
- [ ] Assets recompilados
- [ ] Servidor reiniciado
- [ ] Cookies del navegador limpiadas
- [ ] Carrito funcionando correctamente

## 🎉 Resultado Esperado

Después de aplicar estos cambios:
- ✅ El carrito funciona correctamente
- ✅ Las sesiones persisten entre peticiones
- ✅ Los usuarios invitados pueden usar el carrito
- ✅ Los usuarios autenticados mantienen su carrito
- ✅ No hay errores de CSRF
- ✅ Las cookies se manejan automáticamente

---

**Nota**: Si después de aplicar todos los cambios sigues teniendo problemas, verifica que tu servidor esté corriendo en el puerto correcto y que estés accediendo desde el mismo dominio configurado en `SANCTUM_STATEFUL_DOMAINS`.
