# Documento de Diseño: Vue 3 Checkout Process

## Visión General

Este diseño implementa un proceso de checkout completo en Vue 3 utilizando la Composition API, Pinia para gestión de estado, y TailwindCSS para estilización. El sistema se integra con un backend Laravel existente a través de endpoints API protegidos con Sanctum.

El flujo principal consiste en:
1. Cargar el carrito actual del usuario
2. Capturar información de envío y facturación
3. Seleccionar métodos de envío y pago
4. Validar todos los datos
5. Enviar el pedido al backend
6. Mostrar confirmación del pedido

## Arquitectura

### Arquitectura de Componentes

```
CheckoutPage.vue (Vista Principal)
├── CustomerDataSection.vue
├── ShippingAddressForm.vue
├── BillingAddressForm.vue
├── ShippingMethods.vue
├── PaymentMethods.vue
├── OrderSummary.vue
└── CheckoutActions.vue

OrderSuccess.vue (Página de Confirmación)
├── OrderHeader.vue
├── OrderItemsList.vue
└── OrderDetails.vue
```

### Gestión de Estado

Utilizaremos Pinia para la gestión centralizada del estado del checkout:

```typescript
// stores/checkoutStore.ts
interface CheckoutState {
  cart: Cart | null
  shippingAddress: Address | null
  billingAddress: Address | null
  useSameAddress: boolean
  shippingMethod: ShippingMethod | null
  paymentMethod: PaymentMethod | null
  notes: string
  loading: boolean
  errors: Record<string, string[]>
  order: Order | null
}
```

### Integración con Backend

Todas las llamadas API utilizarán axios configurado con:
- Base URL del backend Laravel
- Credenciales habilitadas para Sanctum (withCredentials: true)
- Interceptores para manejo de errores y tokens CSRF
- Headers apropiados para JSON

**Endpoints utilizados:**
- GET /api/cart - Obtener carrito actual
- POST /api/cart/checkout - Procesar checkout (requiere auth:sanctum)

### Flujo de Datos

```
Usuario → CheckoutPage → CheckoutStore → API Service → Backend Laravel
                ↓              ↓
         Componentes ← Reactive State
```

## Componentes e Interfaces

### CheckoutStore (Pinia Store)

**Responsabilidad:** Gestionar el estado global del proceso de checkout y coordinar las llamadas a la API.

**Estado:**
```typescript
interface CheckoutState {
  cart: Cart | null
  shippingAddress: Address | null
  billingAddress: Address | null
  useSameAddress: boolean
  shippingMethod: ShippingMethod | null
  paymentMethod: PaymentMethod | null
  notes: string
  loading: boolean
  submitting: boolean
  errors: Record<string, string[]>
  order: Order | null
}
```

**Acciones:**
- `loadCart()`: Cargar carrito desde GET /api/cart
- `setShippingAddress(address: Address)`: Actualizar dirección de envío
- `setBillingAddress(address: Address)`: Actualizar dirección de facturación
- `toggleSameAddress(value: boolean)`: Alternar uso de misma dirección
- `setShippingMethod(method: ShippingMethod)`: Seleccionar método de envío
- `setPaymentMethod(method: PaymentMethod)`: Seleccionar método de pago
- `setNotes(notes: string)`: Actualizar notas del pedido
- `validateCheckout()`: Validar todos los datos antes de enviar
- `submitCheckout()`: Enviar pedido al backend
- `clearErrors()`: Limpiar errores de validación
- `reset()`: Resetear el estado del checkout

**Getters:**
- `isValid`: Verificar si todos los datos requeridos están completos
- `totalAmount`: Calcular total incluyendo envío
- `hasErrors`: Verificar si hay errores de validación

### CheckoutPage.vue

**Responsabilidad:** Vista principal que orquesta todo el proceso de checkout.

**Props:** Ninguno

**Emits:** Ninguno

**Estructura:**
```vue
<template>
  <div class="checkout-container">
    <div class="checkout-content">
      <h1>Finalizar Compra</h1>
      
      <!-- Sección de datos del cliente -->
      <CustomerDataSection />
      
      <!-- Formulario de dirección de envío -->
      <section class="checkout-section">
        <h2>Dirección de Envío</h2>
        <ShippingAddressForm />
      </section>
      
      <!-- Formulario de dirección de facturación -->
      <section class="checkout-section">
        <h2>Dirección de Facturación</h2>
        <BillingAddressForm />
      </section>
      
      <!-- Métodos de envío -->
      <section class="checkout-section">
        <h2>Método de Envío</h2>
        <ShippingMethods />
      </section>
      
      <!-- Métodos de pago -->
      <section class="checkout-section">
        <h2>Método de Pago</h2>
        <PaymentMethods />
      </section>
      
      <!-- Notas adicionales -->
      <section class="checkout-section">
        <h2>Notas del Pedido (Opcional)</h2>
        <textarea v-model="notes" />
      </section>
    </div>
    
    <!-- Resumen del pedido (sidebar) -->
    <aside class="checkout-sidebar">
      <OrderSummary />
      <CheckoutActions />
    </aside>
  </div>
</template>
```

**Lógica:**
- Cargar carrito al montar el componente
- Verificar autenticación del usuario
- Redirigir si el carrito está vacío
- Coordinar la navegación entre secciones

### ShippingAddressForm.vue

**Responsabilidad:** Capturar y validar la dirección de envío.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Campos:**
- Nombre completo (required)
- Dirección línea 1 (required)
- Dirección línea 2 (optional)
- Ciudad (required)
- Estado/Provincia (required)
- Código Postal (required)
- País (required, select)

**Validaciones:**
- Todos los campos requeridos deben estar completos
- Código postal debe tener formato válido
- Nombre debe tener al menos 3 caracteres

**Interfaz:**
```typescript
interface Address {
  fullName: string
  addressLine1: string
  addressLine2?: string
  city: string
  state: string
  postalCode: string
  country: string
}
```

### BillingAddressForm.vue

**Responsabilidad:** Capturar y validar la dirección de facturación.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Características:**
- Checkbox "Usar misma dirección de envío"
- Cuando está marcado, ocultar formulario y copiar datos de envío
- Cuando está desmarcado, mostrar formulario independiente
- Mismos campos y validaciones que ShippingAddressForm

### ShippingMethods.vue

**Responsabilidad:** Mostrar y permitir selección de métodos de envío.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Estructura de datos:**
```typescript
interface ShippingMethod {
  id: string
  name: string
  description: string
  estimatedDays: string
  cost: number
}
```

**Métodos disponibles (hardcoded o desde API):**
- Envío Estándar (5-7 días, $5.00)
- Envío Express (2-3 días, $15.00)
- Envío Prioritario (1 día, $25.00)

**UI:**
- Radio buttons o cards seleccionables
- Mostrar nombre, descripción, tiempo estimado y costo
- Resaltar el método seleccionado
- Actualizar total en tiempo real

### PaymentMethods.vue

**Responsabilidad:** Mostrar y permitir selección de métodos de pago.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Estructura de datos:**
```typescript
interface PaymentMethod {
  id: string
  name: string
  description: string
  icon?: string
}
```

**Métodos disponibles:**
- Tarjeta de Crédito/Débito
- PayPal
- Transferencia Bancaria
- Pago contra entrega

**UI:**
- Radio buttons o cards seleccionables
- Mostrar nombre, descripción e icono
- Resaltar el método seleccionado

**Nota:** Este componente solo captura la selección. La integración real con pasarelas de pago se manejará en el backend.

### OrderSummary.vue

**Responsabilidad:** Mostrar resumen del pedido con cálculos actualizados.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Información mostrada:**
- Lista de items del carrito (nombre, cantidad, precio unitario, subtotal)
- Subtotal de productos
- Descuentos aplicados (si hay cupón)
- Costo de envío (basado en método seleccionado)
- Total final

**Características:**
- Actualización reactiva cuando cambian los datos
- Formato de moneda consistente
- Diseño compacto para sidebar

### CheckoutActions.vue

**Responsabilidad:** Botón de acción principal y estados de carga.

**Props:** Ninguno (usa el store)

**Emits:** Ninguno

**Elementos:**
- Botón "Realizar Pedido"
- Indicador de carga durante el envío
- Mensajes de error generales
- Deshabilitar botón si hay validaciones pendientes

**Comportamiento:**
- Validar todos los datos antes de enviar
- Mostrar errores específicos si la validación falla
- Llamar a `submitCheckout()` del store
- Manejar estados de carga y error

### OrderSuccess.vue

**Responsabilidad:** Página de confirmación del pedido exitoso.

**Props:** Ninguno (obtiene datos del store o route params)

**Emits:** Ninguno

**Información mostrada:**
- Mensaje de éxito
- Número de orden
- Resumen de items comprados
- Dirección de envío confirmada
- Método de envío y pago seleccionados
- Total pagado
- Botón "Volver al inicio"

**Lógica:**
- Obtener datos del pedido desde el store o hacer fetch si se recarga la página
- Limpiar el carrito del store
- Prevenir navegación hacia atrás al checkout

## Modelos de Datos

### Cart
```typescript
interface Cart {
  items: CartItem[]
  subtotal: number
  discount: number
  total: number
  coupon?: Coupon
}

interface CartItem {
  uuid: string
  product: Product
  quantity: number
  price: number
  subtotal: number
}

interface Product {
  id: number
  name: string
  slug: string
  image?: string
  price: number
}

interface Coupon {
  code: string
  discount: number
  type: 'percentage' | 'fixed'
}
```

### Address
```typescript
interface Address {
  fullName: string
  addressLine1: string
  addressLine2?: string
  city: string
  state: string
  postalCode: string
  country: string
}
```

### ShippingMethod
```typescript
interface ShippingMethod {
  id: string
  name: string
  description: string
  estimatedDays: string
  cost: number
}
```

### PaymentMethod
```typescript
interface PaymentMethod {
  id: string
  name: string
  description: string
  icon?: string
}
```

### Order (OrderResource del backend)
```typescript
interface Order {
  id: number
  orderNumber: string
  status: string
  items: OrderItem[]
  shippingAddress: Address
  billingAddress: Address
  shippingMethod: ShippingMethod
  paymentMethod: PaymentMethod
  subtotal: number
  shippingCost: number
  discount: number
  total: number
  notes?: string
  createdAt: string
}

interface OrderItem {
  id: number
  product: Product
  quantity: number
  price: number
  subtotal: number
}
```

### CheckoutPayload
```typescript
interface CheckoutPayload {
  shipping_address: {
    full_name: string
    address_line_1: string
    address_line_2?: string
    city: string
    state: string
    postal_code: string
    country: string
  }
  billing_address: {
    full_name: string
    address_line_1: string
    address_line_2?: string
    city: string
    state: string
    postal_code: string
    country: string
  }
  shipping_method: string
  payment_method: string
  notes?: string
}
```