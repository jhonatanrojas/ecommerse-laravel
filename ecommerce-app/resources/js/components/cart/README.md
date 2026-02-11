# Integración del Carrito de Compras - Vue 3

## 📦 Componentes Creados

### 1. **CartStore** (`stores/cart.js`)
Store de Pinia que maneja todo el estado y lógica del carrito.

**Estado:**
- `cart`: Datos del carrito
- `loading`: Estado de carga
- `error`: Mensajes de error
- `isDrawerOpen`: Estado del drawer
- `addingItem`: Estado al añadir producto
- `updatingItem`: UUID del item siendo actualizado
- `removingItem`: UUID del item siendo eliminado
- `applyingCoupon`: Estado al aplicar cupón

**Getters:**
- `items`: Lista de items del carrito
- `itemCount`: Cantidad total de items
- `subtotal`: Subtotal del carrito
- `discount`: Descuento aplicado
- `total`: Total a pagar
- `coupon`: Cupón aplicado
- `isEmpty`: Si el carrito está vacío

**Acciones:**
- `fetchCart()`: Obtiene el carrito del backend
- `addItem(productId, variantId, quantity)`: Añade un producto
- `updateItem(itemUuid, quantity)`: Actualiza cantidad
- `removeItem(itemUuid)`: Elimina un item
- `clearCart()`: Vacía el carrito
- `applyCoupon(code)`: Aplica un cupón
- `removeCoupon()`: Elimina el cupón
- `openDrawer()`: Abre el drawer
- `closeDrawer()`: Cierra el drawer
- `toggleDrawer()`: Alterna el drawer

### 2. **CartDrawer** (`components/cart/CartDrawer.vue`)
Drawer lateral que muestra el carrito completo.

**Características:**
- Lista de productos con imagen, nombre, precio
- Controles de cantidad (+/-)
- Botón para eliminar items
- Campo para aplicar cupones
- Resumen con subtotal, descuento y total
- Botón "Ir al Checkout"
- Botón "Vaciar carrito"
- Animaciones suaves
- Toast de errores

### 3. **CartItem** (`components/cart/CartItem.vue`)
Componente individual para cada item del carrito.

**Características:**
- Imagen del producto
- Nombre y variante
- Precio unitario
- Controles de cantidad
- Botón eliminar
- Subtotal del item
- Advertencia de stock bajo
- Estados de loading

### 4. **CouponInput** (`components/cart/CouponInput.vue`)
Campo para aplicar y gestionar cupones.

**Características:**
- Input para código de cupón
- Botón aplicar con loading
- Muestra cupón aplicado
- Botón para eliminar cupón
- Mensajes de éxito/error

### 5. **CartButton** (`components/cart/CartButton.vue`)
Botón del carrito para el header.

**Características:**
- Ícono del carrito
- Badge con cantidad de items
- Animación al añadir productos
- Abre el drawer al hacer click

### 6. **AddToCartButton** (`components/cart/AddToCartButton.vue`)
Botón reutilizable para añadir productos al carrito.

**Props:**
- `productId`: ID del producto (requerido)
- `variantId`: ID de la variante (opcional)
- `quantity`: Cantidad (default: 1)
- `stock`: Stock disponible (opcional)
- `buttonText`: Texto del botón (default: "Añadir al carrito")
- `buttonClass`: Clases CSS (default: "btn-primary w-full")

**Eventos:**
- `@added`: Emitido cuando se añade exitosamente
- `@error`: Emitido cuando hay un error

**Estados:**
- Loading: Muestra spinner
- Success: Muestra checkmark
- Out of Stock: Deshabilitado
- Default: Listo para añadir

### 7. **CartToast** (`components/cart/CartToast.vue`)
Notificaciones toast para feedback visual.

**Props:**
- `type`: 'success' | 'error' | 'info'
- `title`: Título (opcional)
- `message`: Mensaje (requerido)
- `duration`: Duración en ms (default: 3000)
- `show`: Mostrar/ocultar

## 🚀 Uso

### En el Home (Ya integrado)

El carrito ya está completamente integrado en `Home.vue`:

```vue
<template>
  <div>
    <!-- Header con botón del carrito -->
    <CartButton />
    
    <!-- Drawer del carrito -->
    <CartDrawer />
    
    <!-- Toast notifications -->
    <CartToast
      :show="toast.show"
      :type="toast.type"
      :message="toast.message"
      @close="toast.show = false"
    />
  </div>
</template>

<script>
import { useCartStore } from '../stores/cart';

export default {
  setup() {
    const cartStore = useCartStore();
    
    onMounted(() => {
      cartStore.fetchCart();
    });
    
    return { cartStore };
  }
}
</script>
```

### Usar AddToCartButton en cualquier componente

```vue
<template>
  <AddToCartButton
    :product-id="product.id"
    :variant-id="selectedVariant?.id"
    :quantity="quantity"
    :stock="product.stock"
    button-text="Comprar ahora"
    button-class="btn-primary"
    @added="handleAdded"
    @error="handleError"
  />
</template>

<script>
export default {
  methods: {
    handleAdded({ productId, variantId }) {
      console.log('Producto añadido:', productId);
    },
    handleError(error) {
      console.error('Error:', error);
    }
  }
}
</script>
```

### Usar el store directamente

```vue
<script setup>
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();

// Añadir producto
await cartStore.addItem(productId, variantId, quantity);

// Actualizar cantidad
await cartStore.updateItem(itemUuid, newQuantity);

// Eliminar item
await cartStore.removeItem(itemUuid);

// Aplicar cupón
await cartStore.applyCoupon('DESCUENTO10');

// Abrir drawer
cartStore.openDrawer();

// Acceder a datos
console.log(cartStore.itemCount);
console.log(cartStore.total);
console.log(cartStore.items);
</script>
```

## 🎨 Estilos y UX

### Animaciones incluidas:
- Fade in/out del drawer y overlay
- Slide del drawer desde la derecha
- Bounce del badge al añadir productos
- Transiciones suaves en items
- Loading spinners
- Success checkmarks

### Estados visuales:
- Loading states en todos los botones
- Disabled states cuando corresponde
- Error messages con iconos
- Success feedback
- Empty state del carrito
- Stock warnings

### Responsive:
- Mobile: Drawer full width
- Desktop: Drawer 440px
- Touch-friendly buttons
- Optimizado para todas las pantallas

## 🔧 Configuración de Axios

Ya configurado en `bootstrap.js`:

```javascript
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
```

Esto permite que Axios funcione correctamente con Laravel Sanctum para:
- Enviar cookies de sesión
- Manejar tokens CSRF automáticamente
- Funcionar con usuarios invitados y autenticados

## 📡 API Endpoints Utilizados

- `GET /api/cart` - Obtener carrito
- `POST /api/cart/items` - Añadir item
- `PUT /api/cart/items/{uuid}` - Actualizar item
- `DELETE /api/cart/items/{uuid}` - Eliminar item
- `DELETE /api/cart` - Vaciar carrito
- `POST /api/cart/coupon` - Aplicar cupón
- `DELETE /api/cart/coupon` - Eliminar cupón

## 🎯 Características Implementadas

✅ Store reactivo con Pinia
✅ Sincronización automática con backend
✅ Drawer lateral animado
✅ Botón del carrito con badge
✅ Componente reutilizable AddToCartButton
✅ Gestión de cupones
✅ Control de cantidades
✅ Validación de stock
✅ Estados de loading
✅ Manejo de errores
✅ Toast notifications
✅ Animaciones suaves
✅ Responsive design
✅ Soporte para invitados y autenticados
✅ Integración con Sanctum
✅ Feedback visual inmediato

## 🔄 Flujo de Usuario

1. Usuario ve productos en el home
2. Click en "Añadir al carrito"
3. Botón muestra loading
4. Producto se añade al backend
5. Store se actualiza automáticamente
6. Badge del carrito se anima
7. Drawer se abre automáticamente
8. Usuario ve el producto añadido
9. Puede ajustar cantidades
10. Puede aplicar cupones
11. Click en "Ir al Checkout"

## 🐛 Manejo de Errores

Todos los métodos del store retornan:

```javascript
{
  success: boolean,
  error?: string,
  message?: string
}
```

Los errores se muestran:
- En el drawer (toast interno)
- En componentes individuales
- En el CartToast global

## 📝 Notas Importantes

1. **Pinia instalado**: Ya está instalado y configurado
2. **Bootstrap.js actualizado**: Axios configurado para Sanctum
3. **Home.vue actualizado**: Carrito integrado
4. **FeaturedProductsSection actualizado**: Botones funcionales
5. **Componentes globales**: Registrados en home-app.js

## 🚀 Próximos Pasos

Para usar en otras páginas:

1. Importar Pinia en el entry point
2. Registrar componentes necesarios
3. Usar `useCartStore()` en componentes
4. Añadir `<CartDrawer />` en el layout

## 💡 Tips

- El carrito se sincroniza automáticamente con el backend
- Los cambios persisten entre sesiones
- Funciona para usuarios invitados y autenticados
- El drawer se puede abrir/cerrar desde cualquier lugar
- Los componentes son completamente reutilizables
