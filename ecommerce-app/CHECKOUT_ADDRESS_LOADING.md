# Carga Automática de Direcciones en Checkout

## Problema Resuelto

El checkout no cargaba automáticamente las direcciones guardadas del usuario. Los usuarios tenían que volver a ingresar sus datos de dirección cada vez que hacían una compra.

## Cambios Realizados

### 1. Store de Checkout (`resources/js/stores/checkout.js`)

Se agregó el método `loadUserAddresses()` que:
- Obtiene las direcciones guardadas del usuario autenticado
- Busca la dirección de envío por defecto (o la primera disponible)
- Busca la dirección de facturación por defecto (o la primera disponible)
- Pre-llena los formularios con estos datos
- Maneja el formato de los campos (address_line_1 vs address_line1)

```javascript
async loadUserAddresses() {
  try {
    const response = await checkoutService.getUserAddresses();
    const addresses = response.data || response;
    
    if (addresses && addresses.length > 0) {
      // Buscar dirección de envío por defecto
      const defaultShipping = addresses.find(addr => 
        addr.type === 'shipping' && addr.is_default
      );
      
      // Pre-llenar formulario de envío
      if (shippingAddr) {
        this.shippingAddress = {
          fullName: `${shippingAddr.first_name} ${shippingAddr.last_name}`,
          addressLine1: shippingAddr.address_line_1,
          // ... más campos
        };
      }
      
      // Similar para dirección de facturación
    }
    
    return { success: true };
  } catch (error) {
    // Si no está autenticado o no tiene direcciones, continuar
    return { success: false };
  }
}
```

### 2. Servicio de Checkout (`resources/js/services/checkoutService.js`)

Se agregó el método `getUserAddresses()`:

```javascript
async getUserAddresses() {
  const response = await api.get('/customer/addresses');
  return response.data;
}
```

### 3. Página de Checkout (`resources/js/Pages/CheckoutPage.vue`)

Se modificó el `onMounted` para cargar las direcciones si el usuario está autenticado:

```javascript
onMounted(async () => {
  const authStore = useAuthStore();
  await authStore.checkAuth();

  await checkoutStore.loadStoreConfig();
  await checkoutStore.loadCart();

  // Cargar direcciones del usuario si está autenticado
  if (authStore.isAuthenticated) {
    await checkoutStore.loadUserAddresses();
  }

  // ... resto del código
});
```

### 4. Formularios de Dirección

Se agregaron watchers en ambos formularios para reaccionar a los cambios del store:

**ShippingAddressForm.vue:**
```javascript
watch(() => checkoutStore.shippingAddress, (newAddress) => {
  if (newAddress.fullName && !localAddress.fullName) {
    Object.assign(localAddress, newAddress);
  }
}, { deep: true, immediate: true });
```

**BillingAddressForm.vue:**
```javascript
watch(() => checkoutStore.billingAddress, (newAddress) => {
  if (newAddress.fullName && !localAddress.fullName && !useSameAddress.value) {
    Object.assign(localAddress, newAddress);
  }
}, { deep: true, immediate: true });
```

## Comportamiento

### Usuario Autenticado con Direcciones Guardadas

1. Al entrar al checkout, se cargan automáticamente las direcciones
2. Se usa la dirección de envío marcada como "por defecto" (o la primera disponible)
3. Se usa la dirección de facturación marcada como "por defecto" (o la primera disponible)
4. Si no hay dirección de facturación, se usa la de envío
5. Los formularios se pre-llenan con estos datos
6. El usuario puede modificar los datos si lo desea

### Usuario Autenticado sin Direcciones

1. Los formularios aparecen vacíos
2. El usuario ingresa sus datos normalmente
3. Al completar la compra, puede guardar las direcciones para futuras compras

### Usuario Invitado (Guest Checkout)

1. Los formularios aparecen vacíos
2. El usuario ingresa sus datos para esta compra
3. No se guardan las direcciones

## Prioridad de Direcciones

1. Dirección marcada como "por defecto" (`is_default = true`)
2. Primera dirección del tipo correspondiente (shipping/billing)
3. Si no hay dirección de facturación, se usa la de envío

## Formato de Campos

El sistema maneja ambos formatos de nombres de campos:
- `address_line_1` / `address_line_2` (formato de base de datos)
- `addressLine1` / `addressLine2` (formato de frontend)

## Compilar Cambios

Para aplicar los cambios, ejecuta:

```bash
npm run build
```

O en desarrollo:

```bash
npm run dev
```

## Verificación

Para verificar que funciona correctamente:

1. Inicia sesión con un usuario que tenga direcciones guardadas
2. Ve al checkout (`/checkout`)
3. Los formularios de dirección deberían estar pre-llenados con tus datos
4. Verifica que la dirección de envío y facturación sean correctas
5. Puedes modificar los datos si lo deseas antes de completar la compra
