# 🔄 Actualización - Estructura de Datos del Carrito

## ✅ Problema Solucionado

El carrito se registraba correctamente en la base de datos, pero el drawer no se actualizaba porque el store esperaba una estructura de datos diferente a la que devuelve el backend.

## 📊 Estructura de Respuesta del Backend

### Respuesta Actual del API
```json
{
  "success": true,
  "message": "Item added to cart successfully",
  "data": {
    "cart": {
      "uuid": "...",
      "user_id": null,
      "session_id": "...",
      "coupon_code": null,
      "discount_amount": "0.00",
      "items": [
        {
          "uuid": "...",
          "product_id": 1,
          "product_variant_id": null,
          "quantity": 1,
          "price": "44.99",
          "subtotal": 44.99,
          "product": {
            "id": 1,
            "name": "Producto",
            "slug": "producto",
            "price": "44.99",
            "stock": 100,
            "image": "/storage/products/image.jpg"
          },
          "variant": null
        }
      ]
    },
    "summary": {
      "subtotal": 89.98,
      "discount": 0,
      "tax": 8.998,
      "shipping_cost": 10,
      "total": 108.978,
      "item_count": 2,
      "coupon_code": null
    }
  }
}
```

## 🔧 Cambios Realizados

### 1. Store Actualizado (`resources/js/stores/cart.js`)

**Antes:**
```javascript
state: () => ({
  cart: null,  // Esperaba todo en un solo objeto
  // ...
}),

getters: {
  items: (state) => state.cart?.items || [],
  itemCount: (state) => state.cart?.items?.reduce(...) || 0,
  subtotal: (state) => state.cart?.subtotal || 0,
  // ...
}
```

**Después:**
```javascript
state: () => ({
  cart: null,    // Contiene items y metadata del carrito
  summary: null, // Contiene totales y resumen
  // ...
}),

getters: {
  items: (state) => state.cart?.items || [],
  itemCount: (state) => state.summary?.item_count || 0,
  subtotal: (state) => state.summary?.subtotal || 0,
  discount: (state) => state.summary?.discount || 0,
  tax: (state) => state.summary?.tax || 0,
  shippingCost: (state) => state.summary?.shipping_cost || 0,
  total: (state) => state.summary?.total || 0,
  // ...
}
```

### 2. Métodos del Store Actualizados

Todos los métodos ahora extraen correctamente `cart` y `summary`:

```javascript
async addItem(productId, variantId = null, quantity = 1) {
  // ...
  const response = await axios.post('/api/cart/items', { /* ... */ });
  
  const data = response.data.data || response.data;
  this.cart = data.cart;      // ✅ Extrae cart
  this.summary = data.summary; // ✅ Extrae summary
  
  this.isDrawerOpen = true;
  return { success: true };
}
```

### 3. CartItem Actualizado

**Cambio en la imagen:**
```vue
<!-- Antes -->
<img v-if="item.product?.image_url" :src="item.product.image_url" />

<!-- Después -->
<img v-if="item.product?.image" :src="item.product.image" />
```

**Cambio en validación de stock:**
```javascript
// Antes
:disabled="... || (item.variant?.stock !== null && item.quantity >= item.variant.stock) || ..."

// Después
:disabled="... || (availableStock !== null && item.quantity >= availableStock)"
```

## 📝 Nuevos Getters Disponibles

El store ahora expone estos getters:

```javascript
cartStore.items          // Array de items del carrito
cartStore.itemCount      // Cantidad total de items
cartStore.subtotal       // Subtotal sin descuentos
cartStore.discount       // Descuento aplicado
cartStore.tax            // Impuestos
cartStore.shippingCost   // Costo de envío
cartStore.total          // Total a pagar
cartStore.coupon         // Cupón aplicado (si existe)
cartStore.isEmpty        // true si el carrito está vacío
```

## 🚀 Cómo Usar

### En Componentes Vue

```vue
<script setup>
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();
</script>

<template>
  <div>
    <!-- Cantidad de items -->
    <p>Items: {{ cartStore.itemCount }}</p>
    
    <!-- Subtotal -->
    <p>Subtotal: ${{ cartStore.subtotal.toFixed(2) }}</p>
    
    <!-- Descuento -->
    <p v-if="cartStore.discount > 0">
      Descuento: -${{ cartStore.discount.toFixed(2) }}
    </p>
    
    <!-- Impuestos -->
    <p>Impuestos: ${{ cartStore.tax.toFixed(2) }}</p>
    
    <!-- Envío -->
    <p>Envío: ${{ cartStore.shippingCost.toFixed(2) }}</p>
    
    <!-- Total -->
    <p class="font-bold">Total: ${{ cartStore.total.toFixed(2) }}</p>
    
    <!-- Lista de items -->
    <div v-for="item in cartStore.items" :key="item.uuid">
      <p>{{ item.product.name }} x {{ item.quantity }}</p>
      <p>${{ item.subtotal.toFixed(2) }}</p>
    </div>
  </div>
</template>
```

## ✅ Verificación

Para verificar que todo funciona correctamente:

1. **Recompilar assets:**
   ```bash
   npm run dev
   ```

2. **Limpiar caché del navegador:**
   - F12 > Application > Cookies > Eliminar todas
   - Recargar página (F5)

3. **Probar añadir producto:**
   - Click en "Añadir al carrito"
   - El drawer debería abrirse automáticamente
   - Deberías ver el producto con su imagen
   - El contador del badge debería actualizarse
   - Los totales deberían mostrarse correctamente

4. **Verificar en consola:**
   ```javascript
   // Abrir consola (F12) y ejecutar:
   console.log(window.cartStore?.cart);
   console.log(window.cartStore?.summary);
   ```

## 🔍 Debugging

Si el drawer aún no se actualiza:

### 1. Verificar Respuesta del API
Abre DevTools (F12) > Network > Añade un producto:
- Busca la petición `POST /api/cart/items`
- Verifica que la respuesta tenga `data.cart` y `data.summary`

### 2. Verificar Store en Consola
```javascript
// En la consola del navegador
const { useCartStore } = await import('./resources/js/stores/cart.js');
const cartStore = useCartStore();

console.log('Cart:', cartStore.cart);
console.log('Summary:', cartStore.summary);
console.log('Items:', cartStore.items);
console.log('Item Count:', cartStore.itemCount);
console.log('Total:', cartStore.total);
```

### 3. Verificar que Pinia está Activo
```javascript
// En la consola
console.log(window.__PINIA__); // Debería mostrar el estado de Pinia
```

## 📚 Archivos Modificados

1. ✅ `resources/js/stores/cart.js` - Store actualizado con cart y summary separados
2. ✅ `resources/js/components/cart/CartItem.vue` - Actualizado para usar `item.product.image`

## 🎯 Resultado Esperado

Después de estos cambios:
- ✅ El drawer se abre automáticamente al añadir productos
- ✅ Los items se muestran con sus imágenes
- ✅ Las cantidades se actualizan correctamente
- ✅ Los totales se calculan y muestran correctamente
- ✅ El badge del carrito muestra la cantidad correcta
- ✅ Los cupones funcionan correctamente
- ✅ Todo persiste en la base de datos

## 💡 Notas Importantes

1. **Estructura de Datos**: El backend devuelve `cart` (con items) y `summary` (con totales) por separado. El store ahora maneja ambos correctamente.

2. **Imágenes**: El backend devuelve `image` (no `image_url`) en el objeto product.

3. **Totales**: Los totales (subtotal, discount, tax, shipping_cost, total) vienen en el objeto `summary`, no en `cart`.

4. **Item Count**: El conteo de items viene en `summary.item_count`, no se calcula sumando cantidades.

---

**¡El carrito ahora debería funcionar perfectamente!** 🎉
