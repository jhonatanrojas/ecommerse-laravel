# Guía de Integración del Checkout Vue 3

## ✅ Pasos Completados

### 1. Configuración de Vite ✓
- Agregado `resources/js/checkout-app.js` a la configuración de Vite
- El archivo será compilado automáticamente con `npm run dev` o `npm run build`

### 2. Vista Blade Creada ✓
- Archivo: `resources/views/checkout.blade.php`
- Incluye el script compilado de Vue y los estilos de Tailwind

### 3. Rutas Web Configuradas ✓
- `GET /checkout` - Página principal del checkout
- `GET /order-success/{orderId?}` - Página de confirmación del pedido

### 4. Backend Actualizado ✓
- **CheckoutRequest** actualizado para aceptar direcciones completas
- **CheckoutController** modificado para crear direcciones automáticamente
- Soporta tanto IDs de direcciones existentes como direcciones nuevas

## 🚀 Cómo Usar

### Paso 1: Compilar Assets

```bash
npm run dev
# o para producción
npm run build
```

### Paso 2: Verificar Rutas API

Las siguientes rutas deben estar disponibles:

```
GET  /api/cart                  - Obtener carrito
POST /api/cart/items            - Agregar item
PUT  /api/cart/items/{uuid}     - Actualizar cantidad
DELETE /api/cart/items/{uuid}   - Eliminar item
POST /api/cart/coupon           - Aplicar cupón
DELETE /api/cart/coupon         - Eliminar cupón
POST /api/cart/checkout         - Procesar checkout (requiere auth:sanctum)
```

### Paso 3: Probar el Flujo

1. **Agregar productos al carrito**
   ```bash
   curl -X POST http://localhost:8000/api/cart/items \
     -H "Content-Type: application/json" \
     -d '{
       "product_id": 1,
       "quantity": 2
     }'
   ```

2. **Navegar al checkout**
   ```
   http://localhost:8000/checkout
   ```

3. **Completar el formulario**
   - Dirección de envío
   - Dirección de facturación (o usar la misma)
   - Método de envío
   - Método de pago
   - Notas (opcional)

4. **Realizar pedido**
   - El sistema validará todos los campos
   - Enviará el pedido al backend
   - Redirigirá a la página de confirmación

## 📋 Formato de Datos

### Request de Checkout

```json
{
  "shipping_address": {
    "full_name": "Juan Pérez",
    "address_line_1": "Calle Principal 123",
    "address_line_2": "Apartamento 4B",
    "city": "Madrid",
    "state": "Madrid",
    "postal_code": "28001",
    "country": "ES"
  },
  "billing_address": {
    "full_name": "Juan Pérez",
    "address_line_1": "Calle Principal 123",
    "address_line_2": "Apartamento 4B",
    "city": "Madrid",
    "state": "Madrid",
    "postal_code": "28001",
    "country": "ES"
  },
  "shipping_method": "standard",
  "payment_method": "credit_card",
  "notes": "Entregar por la mañana"
}
```

### Response de Checkout

```json
{
  "success": true,
  "message": "Checkout completed successfully",
  "data": {
    "id": 1,
    "orderNumber": "ORD-2024-001",
    "status": "pending",
    "items": [...],
    "shippingAddress": {...},
    "billingAddress": {...},
    "shippingMethod": {...},
    "paymentMethod": {...},
    "subtotal": 100.00,
    "shippingCost": 5.00,
    "discount": 10.00,
    "total": 95.00,
    "notes": "Entregar por la mañana",
    "createdAt": "2024-02-11T10:00:00Z"
  }
}
```

## 🔧 Configuración Adicional

### Métodos de Envío Disponibles

Configurados en `resources/js/stores/checkout.js`:

- `standard` - Envío Estándar (5-7 días, €5.00)
- `express` - Envío Express (2-3 días, €15.00)
- `priority` - Envío Prioritario (1 día, €25.00)

### Métodos de Pago Disponibles

- `credit_card` - Tarjeta de Crédito/Débito
- `paypal` - PayPal
- `bank_transfer` - Transferencia Bancaria
- `cash_on_delivery` - Pago contra entrega

### Países Soportados

Configurados en los componentes de formulario:

- ES - España
- MX - México
- AR - Argentina
- CO - Colombia
- CL - Chile
- PE - Perú
- VE - Venezuela
- US - Estados Unidos

## 🧪 Testing

### Test Manual

1. **Carrito Vacío**
   - Navegar a `/checkout` sin productos
   - Debe mostrar mensaje "Tu carrito está vacío"

2. **Validaciones**
   - Intentar enviar sin completar campos
   - Verificar mensajes de error específicos

3. **Checkout Exitoso**
   - Completar todos los campos
   - Verificar redirección a página de éxito
   - Verificar datos del pedido

4. **Errores del Backend**
   - Simular error 422 (validación)
   - Simular error 500 (servidor)
   - Verificar mensajes de error

### Test con Postman/Insomnia

```bash
# 1. Obtener CSRF Cookie
GET http://localhost:8000/sanctum/csrf-cookie

# 2. Login (si es necesario)
POST http://localhost:8000/api/login
{
  "email": "user@example.com",
  "password": "password"
}

# 3. Agregar al carrito
POST http://localhost:8000/api/cart/items
{
  "product_id": 1,
  "quantity": 2
}

# 4. Checkout
POST http://localhost:8000/api/cart/checkout
{
  "shipping_address": {...},
  "billing_address": {...},
  "shipping_method": "standard",
  "payment_method": "credit_card"
}
```

## 🐛 Troubleshooting

### Error 419 - CSRF Token Mismatch

**Solución:**
1. Verificar que Sanctum esté configurado en `config/sanctum.php`
2. Asegurarse de que `SESSION_DOMAIN` esté configurado en `.env`
3. Verificar que axios tenga `withCredentials: true`

```javascript
// resources/js/services/api.js
const api = axios.create({
  withCredentials: true,
  withXSRFToken: true,
});
```

### Error 401 - Unauthorized

**Solución:**
1. El checkout requiere autenticación
2. Usuario debe estar logueado
3. Verificar que el token de Sanctum sea válido

### Carrito no se carga

**Solución:**
1. Verificar que el endpoint `/api/cart` responda correctamente
2. Revisar la consola del navegador para errores
3. Verificar que el formato de respuesta sea correcto

### Validación falla en el backend

**Solución:**
1. Verificar que los nombres de campos coincidan
2. Frontend envía: `shipping_address.full_name`
3. Backend espera: `shipping_address.full_name`
4. Revisar `app/Http/Requests/Cart/CheckoutRequest.php`

## 📝 Notas Importantes

### Autenticación

- El checkout **requiere autenticación** (`auth:sanctum`)
- Usuarios invitados deben iniciar sesión primero
- El componente `CustomerDataSection` muestra el estado de autenticación

### Direcciones

- El sistema acepta direcciones completas (no requiere IDs)
- Si el usuario está autenticado, las direcciones se guardan automáticamente
- Las direcciones se asocian al usuario para uso futuro

### Sesión del Carrito

- El carrito se mantiene por sesión
- Al autenticarse, el carrito de sesión se asocia al usuario
- Al hacer logout, el carrito permanece en la sesión

### Moneda

- Por defecto usa EUR (Euro)
- Puedes cambiar en los componentes:
  ```javascript
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-ES', {
      style: 'currency',
      currency: 'EUR', // Cambiar aquí
    }).format(amount);
  };
  ```

## 🎨 Personalización

### Cambiar Colores

Los componentes usan clases de Tailwind. Para cambiar el color principal:

```vue
<!-- De blue-600 a otro color -->
<button class="bg-purple-600 hover:bg-purple-700">
  Realizar Pedido
</button>
```

### Agregar Campos Personalizados

1. Actualizar el store en `resources/js/stores/checkout.js`
2. Agregar campo al formulario correspondiente
3. Actualizar `CheckoutRequest.php` con las validaciones
4. Modificar `CheckoutController.php` para procesar el campo

### Cambiar Métodos de Envío/Pago

Editar `resources/js/stores/checkout.js`:

```javascript
availableShippingMethods: [
  {
    id: 'custom',
    name: 'Mi Método Personalizado',
    description: 'Descripción',
    estimatedDays: '3-5 días',
    cost: 10.00,
  },
]
```

## 📚 Recursos Adicionales

- [Vue 3 Documentation](https://vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [TailwindCSS Documentation](https://tailwindcss.com/)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)

## ✅ Checklist de Producción

Antes de desplegar a producción:

- [ ] Compilar assets con `npm run build`
- [ ] Configurar variables de entorno correctas
- [ ] Verificar configuración de Sanctum
- [ ] Probar flujo completo de checkout
- [ ] Verificar integración con pasarela de pago real
- [ ] Configurar emails de confirmación de pedido
- [ ] Implementar logging de errores
- [ ] Configurar monitoreo de transacciones
- [ ] Realizar pruebas de carga
- [ ] Verificar seguridad (HTTPS, CSRF, XSS)

## 🎉 ¡Listo!

El sistema de checkout está completamente integrado y listo para usar. Si encuentras algún problema, revisa esta guía o consulta los logs de Laravel y la consola del navegador.
