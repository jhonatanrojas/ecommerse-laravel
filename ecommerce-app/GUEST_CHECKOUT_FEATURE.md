# Feature: Guest Checkout Configuration

## 📋 Resumen

Se ha implementado la funcionalidad para permitir o requerir autenticación en el proceso de checkout, configurable desde el panel de administración.

## ✅ Cambios Implementados

### 1. Base de Datos

**Migración**: `2026_02_11_052224_add_guest_checkout_to_store_settings_table.php`

- Agregado campo `allow_guest_checkout` (boolean, default: false) a la tabla `store_settings`

```bash
php artisan migrate
```

### 2. Modelo

**Archivo**: `app/Models/StoreSetting.php`

- Agregado `allow_guest_checkout` a `$fillable`
- Agregado cast a boolean en `$casts`

### 3. Backend - API

#### CheckoutController
**Archivo**: `app/Http/Controllers/Api/CheckoutController.php`

- Verifica configuración de `allow_guest_checkout`
- Si está deshabilitado y el usuario no está autenticado, retorna error 401
- Si está habilitado, permite checkout de invitados
- Maneja direcciones para usuarios autenticados e invitados

#### StoreConfigController (Nuevo)
**Archivo**: `app/Http/Controllers/Api/StoreConfigController.php`

- Endpoint público: `GET /api/store-config`
- Retorna configuración de la tienda incluyendo `allow_guest_checkout`

#### CheckoutRequest
**Archivo**: `app/Http/Requests/Cart/CheckoutRequest.php`

- Actualizado para aceptar direcciones completas (no solo IDs)
- Validaciones para direcciones de invitados

### 4. Rutas

**Archivo**: `routes/api.php`

- Removido middleware `auth:sanctum` de `/api/cart/checkout`
- Agregado endpoint `GET /api/store-config`

### 5. Frontend - Vue 3

#### Checkout Service
**Archivo**: `resources/js/services/checkoutService.js`

- Agregado método `getStoreConfig()` para obtener configuración

#### Checkout Store
**Archivo**: `resources/js/stores/checkout.js`

- Agregado estado `storeConfig`
- Agregado getter `allowGuestCheckout`
- Agregado acción `loadStoreConfig()`

#### CustomerDataSection
**Archivo**: `resources/js/components/checkout/CustomerDataSection.vue`

- Muestra mensaje diferente según configuración
- Si guest checkout está deshabilitado: "Inicio de sesión requerido"
- Si guest checkout está habilitado: "Checkout como invitado"

#### CheckoutPage
**Archivo**: `resources/js/Pages/CheckoutPage.vue`

- Carga configuración de la tienda al montar
- Redirige a login si guest checkout está deshabilitado y usuario no autenticado

### 6. Panel de Administración

#### Vista de Configuración
**Archivo**: `resources/views/admin/settings/store/edit.blade.php`

- Agregado checkbox "Permitir Checkout de Invitados"
- Descripción clara de la funcionalidad

#### Request de Validación
**Archivo**: `app/Http/Requests/UpdateStoreSettingRequest.php`

- Agregada validación para `allow_guest_checkout`

## 🎯 Cómo Funciona

### Flujo con Guest Checkout Deshabilitado (Default)

1. Usuario intenta acceder a `/checkout`
2. Frontend carga configuración: `allow_guest_checkout = false`
3. Si usuario NO está autenticado:
   - Frontend redirige a `/login?redirect=/checkout`
   - Backend retorna 401 si intenta hacer checkout
4. Si usuario está autenticado:
   - Puede completar el checkout normalmente

### Flujo con Guest Checkout Habilitado

1. Usuario intenta acceder a `/checkout`
2. Frontend carga configuración: `allow_guest_checkout = true`
3. Usuario puede proceder sin autenticación
4. Al hacer checkout:
   - Proporciona direcciones completas en el formulario
   - Backend procesa el pedido sin requerir autenticación
   - Direcciones NO se guardan (usuario invitado)

## 🔧 Configuración

### Habilitar Guest Checkout

1. Ir al panel de administración
2. Navegar a **Configuración de la Tienda**
3. Marcar checkbox **"Permitir Checkout de Invitados"**
4. Guardar cambios

### Deshabilitar Guest Checkout

1. Ir al panel de administración
2. Navegar a **Configuración de la Tienda**
3. Desmarcar checkbox **"Permitir Checkout de Invitados"**
4. Guardar cambios

## 📊 Endpoints API

### GET /api/store-config

Obtiene la configuración pública de la tienda.

**Response:**
```json
{
  "success": true,
  "data": {
    "allow_guest_checkout": false,
    "store_name": "Mi Tienda",
    "currency": "EUR",
    "currency_symbol": "€"
  }
}
```

### POST /api/cart/checkout

Procesa el checkout (ahora sin requerir autenticación obligatoria).

**Comportamiento:**
- Si `allow_guest_checkout = false` y usuario no autenticado → Error 401
- Si `allow_guest_checkout = true` → Permite checkout sin autenticación
- Si usuario autenticado → Siempre permite checkout

## 🧪 Testing

### Test 1: Guest Checkout Deshabilitado

```bash
# 1. Configurar en admin: allow_guest_checkout = false
# 2. Cerrar sesión
# 3. Agregar productos al carrito
# 4. Ir a /checkout
# Resultado esperado: Redirige a /login
```

### Test 2: Guest Checkout Habilitado

```bash
# 1. Configurar en admin: allow_guest_checkout = true
# 2. Cerrar sesión
# 3. Agregar productos al carrito
# 4. Ir a /checkout
# Resultado esperado: Permite completar checkout sin login
```

### Test 3: Usuario Autenticado

```bash
# 1. Iniciar sesión
# 2. Agregar productos al carrito
# 3. Ir a /checkout
# Resultado esperado: Permite checkout (independiente de la configuración)
```

## 🔐 Seguridad

- La configuración se valida en el backend
- No se puede bypassear la restricción desde el frontend
- Las direcciones de invitados no se guardan en la base de datos
- Los pedidos de invitados se asocian a la sesión

## 📝 Notas Importantes

### Para Usuarios Autenticados

- Las direcciones se guardan automáticamente
- Pueden reutilizar direcciones en futuros pedidos
- El historial de pedidos está disponible

### Para Usuarios Invitados

- Deben ingresar direcciones completas cada vez
- Las direcciones NO se guardan
- No tienen historial de pedidos accesible
- El pedido se asocia a la sesión

## 🎨 UI/UX

### Mensajes Mostrados

**Guest Checkout Deshabilitado:**
```
🔒 Inicio de sesión requerido
Debes iniciar sesión para completar tu compra. [Inicia sesión]
```

**Guest Checkout Habilitado:**
```
ℹ️ Checkout como invitado
¿Ya tienes una cuenta? [Inicia sesión] para una experiencia más rápida.
```

## 🚀 Próximos Pasos Sugeridos

1. **Email de Confirmación para Invitados**
   - Solicitar email en el checkout
   - Enviar confirmación del pedido

2. **Tracking para Invitados**
   - Generar código de tracking
   - Permitir consultar pedido sin login

3. **Conversión a Usuario**
   - Opción de crear cuenta después del checkout
   - Asociar pedido al nuevo usuario

4. **Analytics**
   - Trackear tasa de conversión con/sin guest checkout
   - Comparar abandono de carrito

## ✅ Checklist de Verificación

- [x] Migración ejecutada
- [x] Modelo actualizado
- [x] Backend actualizado
- [x] Frontend actualizado
- [x] Panel de admin actualizado
- [x] Rutas configuradas
- [x] Validaciones implementadas
- [x] Documentación creada

## 🐛 Troubleshooting

### Error: "allow_guest_checkout column not found"

```bash
# Ejecutar migración
php artisan migrate

# Si persiste, verificar que la migración se ejecutó
php artisan migrate:status
```

### Frontend no refleja cambios

```bash
# Limpiar cache de Laravel
php artisan config:clear
php artisan cache:clear

# Recompilar assets
npm run dev
```

### Checkout sigue requiriendo login

1. Verificar en base de datos: `SELECT allow_guest_checkout FROM store_settings;`
2. Verificar que el checkbox esté marcado en admin
3. Limpiar cache del navegador
4. Verificar consola del navegador para errores

---

**Fecha de Implementación**: 11 de Febrero, 2026
**Versión**: 1.0.0
