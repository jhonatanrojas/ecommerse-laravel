# Vue 3 Checkout Process

Sistema completo de checkout implementado en Vue 3 con Composition API, Pinia y TailwindCSS.

## Estructura de Archivos

```
resources/js/
├── components/checkout/
│   ├── BillingAddressForm.vue      # Formulario de dirección de facturación
│   ├── CheckoutActions.vue         # Botón de realizar pedido y validaciones
│   ├── CustomerDataSection.vue     # Información del usuario autenticado
│   ├── OrderSummary.vue            # Resumen del pedido con cálculos
│   ├── PaymentMethods.vue          # Selección de método de pago
│   └── ShippingAddressForm.vue     # Formulario de dirección de envío
│   └── ShippingMethods.vue         # Selección de método de envío
├── Pages/
│   ├── CheckoutPage.vue            # Vista principal del checkout
│   └── OrderSuccess.vue            # Página de confirmación del pedido
├── stores/
│   └── checkout.js                 # Pinia store para gestión de estado
├── services/
│   ├── api.js                      # Configuración de axios con Sanctum
│   └── checkoutService.js          # Servicio API para checkout
├── types/
│   └── checkout.js                 # Definiciones de tipos JSDoc
├── router/
│   └── index.js                    # Configuración de Vue Router
└── checkout-app.js                 # Punto de entrada de la aplicación
```

## Características

### 1. Gestión de Estado (Pinia Store)
- Estado centralizado del carrito y checkout
- Validaciones antes de enviar
- Manejo de errores del backend
- Integración con notificaciones toast

### 2. Componentes Reutilizables
- **ShippingAddressForm**: Captura dirección de envío con validaciones
- **BillingAddressForm**: Dirección de facturación con opción "usar misma dirección"
- **ShippingMethods**: Selección de método de envío con actualización de total
- **PaymentMethods**: Selección de método de pago
- **OrderSummary**: Resumen reactivo del pedido
- **CheckoutActions**: Botón de envío con estados de carga

### 3. Integración con Backend
- Axios configurado con Sanctum para autenticación
- Interceptores para manejo de errores y CSRF
- Endpoints:
  - `GET /api/cart` - Obtener carrito
  - `POST /api/cart/checkout` - Procesar checkout

### 4. Validaciones
- Validación de campos requeridos en formularios
- Validación completa antes de enviar al backend
- Mensajes de error específicos por campo
- Feedback visual en tiempo real

### 5. UX/UI
- Diseño responsive con TailwindCSS
- Estados de carga y deshabilitado
- Notificaciones toast para feedback
- Animaciones suaves
- Diseño limpio y moderno

## Uso

### 1. Instalación

Asegúrate de tener las dependencias necesarias:

```bash
npm install vue@^3 pinia vue-router axios
```

### 2. Configuración de Rutas

Las rutas están configuradas en `resources/js/router/index.js`:

- `/checkout` - Página principal del checkout
- `/order-success/:orderId?` - Página de confirmación

### 3. Integración en Laravel

Crea una vista Blade para el checkout:

```blade
<!-- resources/views/checkout.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/checkout-app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

Agrega la ruta en `routes/web.php`:

```php
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');
```

### 4. Uso del Store

```javascript
import { useCheckoutStore } from '@/stores/checkout';

const checkoutStore = useCheckoutStore();

// Cargar carrito
await checkoutStore.loadCart();

// Establecer dirección de envío
checkoutStore.setShippingAddress({
  fullName: 'Juan Pérez',
  addressLine1: 'Calle Principal 123',
  city: 'Madrid',
  state: 'Madrid',
  postalCode: '28001',
  country: 'ES',
});

// Seleccionar método de envío
checkoutStore.setShippingMethod({
  id: 'standard',
  name: 'Envío Estándar',
  cost: 5.00,
});

// Enviar checkout
const result = await checkoutStore.submitCheckout();
if (result.success) {
  // Redirigir a página de éxito
  router.push({ name: 'order-success', params: { orderId: result.order.id } });
}
```

### 5. Personalización

#### Métodos de Envío

Edita `resources/js/stores/checkout.js` para modificar los métodos disponibles:

```javascript
availableShippingMethods: [
  {
    id: 'standard',
    name: 'Envío Estándar',
    description: 'Entrega en 5-7 días hábiles',
    estimatedDays: '5-7 días',
    cost: 5.00,
  },
  // Agregar más métodos...
]
```

#### Métodos de Pago

Similar a los métodos de envío, edita `availablePaymentMethods` en el store.

#### Estilos

Los componentes usan TailwindCSS. Puedes personalizar los estilos modificando las clases en cada componente.

## API Backend Requerida

El backend debe implementar los siguientes endpoints:

### GET /api/cart

Respuesta esperada:
```json
{
  "data": {
    "cart": {
      "items": [
        {
          "uuid": "abc-123",
          "product": {
            "id": 1,
            "name": "Producto",
            "price": 10.00,
            "image": "/images/product.jpg"
          },
          "quantity": 2,
          "price": 10.00,
          "subtotal": 20.00
        }
      ],
      "coupon_code": "DESCUENTO10"
    },
    "summary": {
      "item_count": 2,
      "subtotal": 20.00,
      "discount": 2.00,
      "tax": 0,
      "shipping_cost": 0,
      "total": 18.00
    }
  }
}
```

### POST /api/cart/checkout

Payload:
```json
{
  "shipping_address": {
    "full_name": "Juan Pérez",
    "address_line_1": "Calle Principal 123",
    "address_line_2": "",
    "city": "Madrid",
    "state": "Madrid",
    "postal_code": "28001",
    "country": "ES"
  },
  "billing_address": { /* mismo formato */ },
  "shipping_method": "standard",
  "payment_method": "credit_card",
  "notes": "Entregar por la mañana"
}
```

Respuesta esperada:
```json
{
  "data": {
    "id": 1,
    "orderNumber": "ORD-2024-001",
    "status": "pending",
    "items": [ /* items del pedido */ ],
    "shippingAddress": { /* dirección */ },
    "billingAddress": { /* dirección */ },
    "shippingMethod": { /* método */ },
    "paymentMethod": { /* método */ },
    "subtotal": 20.00,
    "shippingCost": 5.00,
    "discount": 2.00,
    "total": 23.00,
    "notes": "Entregar por la mañana",
    "createdAt": "2024-02-11T10:00:00Z"
  }
}
```

## Principios SOLID en Backend

El backend debe seguir estos principios:

1. **Single Responsibility**: Cada controlador maneja una responsabilidad específica
2. **Open/Closed**: Extensible sin modificar código existente
3. **Liskov Substitution**: Interfaces consistentes
4. **Interface Segregation**: Interfaces específicas
5. **Dependency Inversion**: Depender de abstracciones

## Testing

Para probar el checkout:

1. Agrega productos al carrito
2. Navega a `/checkout`
3. Completa todos los formularios
4. Selecciona método de envío y pago
5. Haz clic en "Realizar Pedido"
6. Verifica la página de confirmación

## Troubleshooting

### Error 419 (CSRF Token Mismatch)
- Asegúrate de que Sanctum esté configurado correctamente
- Verifica que `withCredentials: true` esté en axios
- Comprueba que el dominio de la API coincida con el frontend

### Carrito vacío
- Verifica que el endpoint `/api/cart` devuelva datos correctos
- Comprueba la consola del navegador para errores

### Validación fallida
- Revisa que todos los campos requeridos estén completos
- Verifica los mensajes de error en la consola

## Contribución

Para agregar nuevas funcionalidades:

1. Crea nuevos componentes en `components/checkout/`
2. Actualiza el store si necesitas nuevo estado
3. Agrega validaciones en el store
4. Actualiza la documentación

## Licencia

Este código es parte del proyecto ecommerce-app.
