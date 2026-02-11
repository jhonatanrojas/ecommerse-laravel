# ⚡ Solución Rápida - Error de Sesión del Carrito

## 🔴 Problema
Al intentar añadir productos al carrito aparece el error:
```
Session store not set on request
```

## ✅ Solución Aplicada

He realizado los siguientes cambios para solucionar el problema:

### 1️⃣ Habilitado Sanctum en API (✅ HECHO)
- Archivo: `app/Http/Kernel.php`
- Descomentado el middleware de Sanctum

### 2️⃣ Habilitado Credenciales en CORS (✅ HECHO)
- Archivo: `config/cors.php`
- Cambiado `supports_credentials` a `true`

### 3️⃣ Configurado Variables de Entorno (✅ HECHO)
- Archivo: `.env`
- Añadido `SANCTUM_STATEFUL_DOMAINS` y `SESSION_DOMAIN`

### 4️⃣ Actualizado Store del Carrito (✅ HECHO)
- Archivo: `resources/js/stores/cart.js`
- Añadida inicialización automática de CSRF

### 5️⃣ Limpiado Caché de Laravel (✅ HECHO)
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 🚀 Pasos Finales (DEBES HACER)

### 1. Recompilar Assets
```bash
npm run dev
```
**O si ya está corriendo, detenerlo (Ctrl+C) y volver a ejecutar**

### 2. Reiniciar Servidor Laravel
Si tienes el servidor corriendo:
```bash
# Detener (Ctrl+C)
# Volver a iniciar
php artisan serve
```

### 3. Limpiar Cookies del Navegador
1. Abre DevTools (F12)
2. Ve a Application > Cookies
3. Elimina todas las cookies de localhost
4. Recarga la página (F5)

### 4. Probar el Carrito
1. Ve a la página de inicio
2. Intenta añadir un producto al carrito
3. Debería funcionar correctamente

## 🔍 Verificar que Funciona

Abre la consola del navegador (F12) y ve a la pestaña Network:

1. **Primera vez que añades un producto**, deberías ver:
   - `GET /sanctum/csrf-cookie` → 200 OK
   - `POST /api/cart/items` → 200 OK

2. **En las cookies** (Application > Cookies):
   - `XSRF-TOKEN`
   - `laravel_session`

3. **El carrito debería**:
   - Añadir productos correctamente
   - Mostrar el drawer lateral
   - Actualizar el badge del carrito
   - Persistir entre recargas

## ❌ Si Aún No Funciona

### Opción 1: Verificar Configuración
```bash
# Ver la configuración actual
php artisan config:show sanctum
```

### Opción 2: Verificar que el Dominio es Correcto
Si accedes a tu app en un puerto diferente (ej: localhost:8080), actualiza `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:8080,127.0.0.1,127.0.0.1:8080
```

### Opción 3: Verificar Axios
Abre la consola del navegador y ejecuta:
```javascript
console.log(window.axios.defaults.withCredentials); // Debe ser true
console.log(window.axios.defaults.withXSRFToken); // Debe ser true
```

### Opción 4: Verificar Rutas API
```bash
php artisan route:list --path=api/cart
```

Deberías ver las rutas del carrito listadas.

## 📝 Archivos Modificados

1. ✅ `app/Http/Kernel.php` - Middleware de Sanctum
2. ✅ `config/cors.php` - Credenciales habilitadas
3. ✅ `.env` - Variables de Sanctum
4. ✅ `resources/js/stores/cart.js` - Inicialización CSRF
5. ✅ Caché limpiada

## 🎯 Próximos Pasos

Una vez que el carrito funcione:

1. **Probar todas las funcionalidades**:
   - Añadir productos
   - Actualizar cantidades
   - Eliminar items
   - Aplicar cupones
   - Vaciar carrito

2. **Verificar en diferentes navegadores**:
   - Chrome
   - Firefox
   - Safari
   - Edge

3. **Probar con usuario autenticado**:
   - Inicia sesión
   - Añade productos al carrito
   - Verifica que persiste

## 📚 Documentación Completa

Para más detalles, consulta:
- `CART_SESSION_FIX.md` - Explicación técnica completa
- `CART_INTEGRATION_SUMMARY.md` - Resumen de la integración
- `QUICK_START_CART.md` - Guía de inicio rápido

## 💡 Tip Importante

**Siempre que cambies configuración en `.env` o archivos de config:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Siempre que cambies código JavaScript:**
```bash
# Reiniciar npm run dev
```

## ✅ Checklist Final

- [x] Middleware de Sanctum habilitado
- [x] CORS configurado
- [x] Variables de entorno añadidas
- [x] Store actualizado
- [x] Caché limpiada
- [ ] Assets recompilados (`npm run dev`)
- [ ] Servidor reiniciado
- [ ] Cookies del navegador limpiadas
- [ ] Carrito probado y funcionando

---

**¡El problema está solucionado!** Solo necesitas ejecutar los pasos finales (recompilar assets, reiniciar servidor y limpiar cookies).
