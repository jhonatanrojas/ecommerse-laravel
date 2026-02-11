# 🚀 Quick Start - Carrito de Compras

## Inicio Rápido en 3 Pasos

### 1️⃣ Compilar Assets
```bash
npm run dev
```

### 2️⃣ Abrir el Navegador
Navega a tu aplicación Laravel (ej: `http://localhost:8000`)

### 3️⃣ ¡Listo!
- El carrito ya está funcionando en el Home
- Click en cualquier botón "Añadir al carrito" en productos destacados
- El drawer se abrirá automáticamente
- Prueba añadir, actualizar cantidades, aplicar cupones

## 🎯 Funcionalidades Disponibles

### En el Home (Ya Integrado)
✅ Botón del carrito en el header con badge
✅ Drawer lateral del carrito
✅ Botones "Añadir al carrito" en productos destacados
✅ Notificaciones toast
✅ Sincronización automática con backend

### Acciones Disponibles
- ➕ Añadir productos al carrito
- 🔢 Ajustar cantidades
- 🗑️ Eliminar items
- 🎟️ Aplicar cupones de descuento
- 🧹 Vaciar carrito
- 💳 Ir al checkout

## 📱 Prueba Estas Acciones

1. **Añadir un producto**
   - Click en el botón del carrito en cualquier producto
   - El drawer se abre automáticamente
   - El badge se anima

2. **Ajustar cantidad**
   - Usa los botones + y - en cada item
   - La cantidad se actualiza en tiempo real

3. **Aplicar cupón**
   - Click en "¿Tienes un cupón?"
   - Ingresa un código (ej: DESCUENTO10)
   - Click en "Aplicar"

4. **Ver el carrito**
   - Click en el ícono del carrito en el header
   - El drawer se abre mostrando todos los items

5. **Eliminar item**
   - Click en el ícono de basura en cualquier item
   - El item se elimina inmediatamente

## 🔧 Usar en Otros Componentes

### Opción 1: Componente AddToCartButton
```vue
<template>
  <AddToCartButton
    :product-id="123"
    button-text="Añadir al carrito"
  />
</template>
```

### Opción 2: Store Directamente
```vue
<script setup>
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();

// Añadir producto
await cartStore.addItem(productId, variantId, quantity);

// Abrir carrito
cartStore.openDrawer();

// Ver cantidad de items
console.log(cartStore.itemCount);
</script>
```

## 📚 Documentación Completa

- **`CART_INTEGRATION_SUMMARY.md`** - Resumen completo de la integración
- **`resources/js/components/cart/README.md`** - Documentación técnica
- **`resources/js/components/cart/USAGE_EXAMPLES.md`** - 10 ejemplos de uso

## 🐛 Solución de Problemas

### El carrito no se muestra
```bash
# Recompilar assets
npm run dev
```

### Error de Axios/CSRF
Verifica que `bootstrap.js` tenga:
```javascript
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
```

### Pinia no está definido
```bash
# Reinstalar dependencias
npm install
```

## 🎨 Personalización Rápida

### Cambiar colores del botón
```vue
<AddToCartButton
  button-class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
/>
```

### Cambiar texto del botón
```vue
<AddToCartButton
  button-text="Comprar ahora"
/>
```

### Cambiar ancho del drawer
En `CartDrawer.vue`, línea 30:
```vue
class="... w-full sm:w-[440px] ..."
<!-- Cambiar 440px por el ancho deseado -->
```

## 🔥 Tips Rápidos

1. **El drawer se abre automáticamente** al añadir un producto
2. **El badge se anima** cuando añades productos
3. **Los cambios se guardan** automáticamente en el backend
4. **Funciona para invitados** y usuarios autenticados
5. **El carrito persiste** entre sesiones

## 📞 Soporte

Si tienes problemas:
1. Revisa la consola del navegador
2. Verifica que el backend esté corriendo
3. Comprueba que las rutas API estén disponibles
4. Lee la documentación completa en los archivos README

## ✅ Checklist de Verificación

- [ ] `npm install` ejecutado
- [ ] `npm run dev` corriendo
- [ ] Backend Laravel corriendo
- [ ] Rutas API `/api/cart` disponibles
- [ ] Página home cargando correctamente
- [ ] Botón del carrito visible en header
- [ ] Productos mostrándose en home
- [ ] Botones "Añadir al carrito" visibles

## 🎉 ¡Todo Listo!

El carrito está completamente integrado y listo para usar. Explora las funcionalidades y personaliza según tus necesidades.

**¡Feliz desarrollo! 🚀**
