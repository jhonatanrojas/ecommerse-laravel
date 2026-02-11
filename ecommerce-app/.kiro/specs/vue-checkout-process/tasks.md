# Tasks: Vue 3 Checkout Process

## Task 1: Configurar Axios y API Service
- [x] 1.1 Crear archivo de configuración de axios con credenciales Sanctum
- [x] 1.2 Implementar interceptores para manejo de errores y CSRF token
- [x] 1.3 Crear servicio API para endpoints del carrito y checkout

## Task 2: Implementar Modelos de Datos TypeScript
- [x] 2.1 Crear interfaces para Cart, CartItem, Product, Coupon
- [x] 2.2 Crear interfaces para Address
- [x] 2.3 Crear interfaces para ShippingMethod y PaymentMethod
- [x] 2.4 Crear interfaces para Order, OrderItem y CheckoutPayload

## Task 3: Implementar CheckoutStore (Pinia)
- [x] 3.1 Crear estructura base del store con estado inicial
- [x] 3.2 Implementar acción loadCart()
- [x] 3.3 Implementar acciones para direcciones (setShippingAddress, setBillingAddress, toggleSameAddress)
- [x] 3.4 Implementar acciones para métodos (setShippingMethod, setPaymentMethod)
- [x] 3.5 Implementar acción validateCheckout()
- [x] 3.6 Implementar acción submitCheckout()
- [x] 3.7 Implementar getters (isValid, totalAmount, hasErrors)
- [x] 3.8 Implementar manejo de errores y estados de carga

## Task 4: Implementar ShippingAddressForm.vue
- [x] 4.1 Crear estructura del componente con todos los campos
- [x] 4.2 Implementar validaciones de campos requeridos
- [x] 4.3 Integrar con checkoutStore
- [x] 4.4 Implementar estilos con TailwindCSS
- [x] 4.5 Agregar mensajes de error por campo

## Task 5: Implementar BillingAddressForm.vue
- [x] 5.1 Crear estructura del componente con checkbox "usar misma dirección"
- [x] 5.2 Implementar lógica para copiar dirección de envío
- [x] 5.3 Implementar formulario independiente con validaciones
- [x] 5.4 Integrar con checkoutStore
- [x] 5.5 Implementar estilos con TailwindCSS

## Task 6: Implementar ShippingMethods.vue
- [x] 6.1 Crear estructura del componente con opciones de envío
- [x] 6.2 Implementar selección de método (radio buttons o cards)
- [x] 6.3 Integrar con checkoutStore
- [x] 6.4 Implementar actualización de total en tiempo real
- [x] 6.5 Implementar estilos con TailwindCSS

## Task 7: Implementar PaymentMethods.vue
- [x] 7.1 Crear estructura del componente con opciones de pago
- [x] 7.2 Implementar selección de método (radio buttons o cards)
- [x] 7.3 Integrar con checkoutStore
- [x] 7.4 Implementar estilos con TailwindCSS

## Task 8: Implementar OrderSummary.vue
- [x] 8.1 Crear estructura del componente
- [x] 8.2 Implementar lista de items del carrito
- [x] 8.3 Implementar cálculos (subtotal, descuentos, envío, total)
- [x] 8.4 Implementar actualización reactiva
- [x] 8.5 Implementar formato de moneda
- [x] 8.6 Implementar estilos con TailwindCSS

## Task 9: Implementar CustomerDataSection.vue
- [x] 9.1 Crear componente para mostrar datos del usuario autenticado
- [x] 9.2 Implementar lógica para detectar autenticación
- [x] 9.3 Mostrar opción de login si no está autenticado
- [x] 9.4 Implementar estilos con TailwindCSS

## Task 10: Implementar CheckoutActions.vue
- [x] 10.1 Crear botón "Realizar Pedido"
- [x] 10.2 Implementar estados de carga y deshabilitado
- [x] 10.3 Implementar validación antes de enviar
- [x] 10.4 Implementar manejo de errores
- [x] 10.5 Implementar estilos con TailwindCSS

## Task 11: Implementar CheckoutPage.vue
- [x] 11.1 Crear estructura principal del layout
- [x] 11.2 Integrar todos los componentes de formulario
- [x] 11.3 Integrar OrderSummary en sidebar
- [x] 11.4 Implementar carga inicial del carrito
- [x] 11.5 Implementar verificación de carrito vacío
- [x] 11.6 Implementar campo de notas
- [x] 11.7 Implementar estilos responsive con TailwindCSS

## Task 12: Implementar OrderSuccess.vue
- [x] 12.1 Crear estructura de la página de confirmación
- [x] 12.2 Implementar visualización del número de orden
- [x] 12.3 Implementar resumen de items comprados
- [x] 12.4 Implementar visualización de direcciones y métodos
- [x] 12.5 Implementar botón "Volver al inicio"
- [x] 12.6 Implementar lógica para obtener datos del pedido
- [x] 12.7 Implementar estilos con TailwindCSS

## Task 13: Configurar Rutas de Vue Router
- [x] 13.1 Agregar ruta para CheckoutPage
- [x] 13.2 Agregar ruta para OrderSuccess
- [x] 13.3 Implementar guards de navegación (verificar carrito no vacío)
- [x] 13.4 Configurar meta información de rutas

## Task 14: Implementar Sistema de Notificaciones
- [x] 14.1 Crear composable o servicio para toasts/alertas
- [x] 14.2 Integrar notificaciones en checkoutStore
- [x] 14.3 Implementar estilos para notificaciones

## Task 15: Testing y Validación
- [x] 15.1 Probar flujo completo de checkout
- [x] 15.2 Probar validaciones de formularios
- [x] 15.3 Probar manejo de errores del backend
- [x] 15.4 Probar responsive design
- [x] 15.5 Probar integración con Sanctum
