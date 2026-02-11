# Requirements Document

## Introduction

Este documento define los requisitos para el proceso completo de checkout del ecommerce, implementado en Vue 3. El sistema permitirá a los usuarios completar sus compras proporcionando información de envío, facturación, seleccionando métodos de envío y pago, y finalmente confirmando su pedido. El backend ya está implementado con Laravel y Sanctum para autenticación.

## Glossary

- **Checkout_System**: El sistema completo de checkout implementado en Vue 3
- **Cart_API**: La API del backend Laravel que gestiona el carrito y checkout
- **User**: Usuario del ecommerce (autenticado o invitado)
- **Checkout_Store**: Store de Pinia o composable que gestiona el estado del checkout
- **Shipping_Address**: Dirección de envío del pedido
- **Billing_Address**: Dirección de facturación del pedido
- **Shipping_Method**: Método de envío seleccionado (estándar, express, etc.)
- **Payment_Method**: Método de pago seleccionado (tarjeta, PayPal, etc.)
- **Order**: Pedido confirmado y procesado
- **OrderResource**: Recurso JSON devuelto por el backend con los datos del pedido

## Requirements

### Requirement 1: Gestión del Estado del Checkout

**User Story:** Como desarrollador, quiero un store centralizado para gestionar el estado del checkout, para que todos los componentes puedan acceder y modificar la información de forma consistente.

#### Acceptance Criteria

1. THE Checkout_Store SHALL mantener el estado del carrito, direcciones de envío y facturación, métodos de envío y pago, notas, estados de carga y errores
2. WHEN se inicializa el Checkout_Store, THE Checkout_Store SHALL cargar los datos del carrito desde la Cart_API
3. WHEN un componente actualiza una dirección, THE Checkout_Store SHALL validar y almacenar la información
4. WHEN un componente actualiza un método de envío o pago, THE Checkout_Store SHALL actualizar el estado correspondiente
5. THE Checkout_Store SHALL proporcionar métodos para loadCart, setShippingAddress, setBillingAddress, setShippingMethod, setPaymentMethod y submitCheckout

### Requirement 2: Carga y Visualización del Carrito

**User Story:** Como usuario, quiero ver el contenido de mi carrito durante el checkout, para que pueda verificar los productos antes de completar la compra.

#### Acceptance Criteria

1. WHEN el User accede a la página de checkout, THE Checkout_System SHALL cargar el carrito desde GET /api/cart
2. WHEN el carrito se carga exitosamente, THE Checkout_System SHALL mostrar los items, subtotal, descuentos aplicados y total
3. WHEN el carrito está vacío, THE Checkout_System SHALL mostrar un mensaje indicando que no hay productos y prevenir el checkout
4. WHEN ocurre un error al cargar el carrito, THE Checkout_System SHALL mostrar un mensaje de error descriptivo

### Requirement 3: Autenticación del Usuario

**User Story:** Como usuario, quiero que el sistema reconozca si estoy autenticado, para que pueda usar mis datos guardados o proporcionar nuevos datos.

#### Acceptance Criteria

1. WHEN el User está autenticado, THE Checkout_System SHALL mostrar los datos del usuario (nombre, email)
2. WHEN el User no está autenticado, THE Checkout_System SHALL solicitar login o permitir checkout como invitado
3. THE Checkout_System SHALL integrar Sanctum para autenticación mediante axios con credenciales
4. WHEN se realiza una petición a /api/cart/checkout, THE Checkout_System SHALL incluir el token de autenticación de Sanctum

### Requirement 4: Gestión de Dirección de Envío

**User Story:** Como usuario, quiero proporcionar mi dirección de envío, para que mi pedido sea entregado en la ubicación correcta.

#### Acceptance Criteria

1. THE Checkout_System SHALL proporcionar un formulario para capturar nombre completo, dirección, ciudad, estado, código postal y país
2. WHEN el User completa el formulario de envío, THE Checkout_System SHALL validar que todos los campos requeridos estén completos
3. WHEN la validación falla, THE Checkout_System SHALL mostrar mensajes de error específicos para cada campo
4. WHEN la validación es exitosa, THE Checkout_System SHALL almacenar la Shipping_Address en el Checkout_Store

### Requirement 5: Gestión de Dirección de Facturación

**User Story:** Como usuario, quiero proporcionar mi dirección de facturación, para que la factura sea emitida correctamente.

#### Acceptance Criteria

1. THE Checkout_System SHALL proporcionar un formulario para capturar dirección de facturación con los mismos campos que la dirección de envío
2. THE Checkout_System SHALL proporcionar un checkbox "Usar misma dirección de envío"
3. WHEN el User marca el checkbox, THE Checkout_System SHALL copiar automáticamente la Shipping_Address a la Billing_Address
4. WHEN el User desmarca el checkbox, THE Checkout_System SHALL mostrar el formulario de facturación independiente
5. WHEN el User completa el formulario de facturación, THE Checkout_System SHALL validar que todos los campos requeridos estén completos

### Requirement 6: Selección de Método de Envío

**User Story:** Como usuario, quiero seleccionar un método de envío, para que pueda elegir entre velocidad y costo de entrega.

#### Acceptance Criteria

1. THE Checkout_System SHALL mostrar las opciones de métodos de envío disponibles con nombre, descripción, tiempo estimado y costo
2. WHEN el User selecciona un Shipping_Method, THE Checkout_System SHALL actualizar el total del pedido incluyendo el costo de envío
3. THE Checkout_System SHALL requerir que un Shipping_Method sea seleccionado antes de proceder al checkout
4. WHEN no hay Shipping_Method seleccionado, THE Checkout_System SHALL mostrar un mensaje de validación

### Requirement 7: Selección de Método de Pago

**User Story:** Como usuario, quiero seleccionar un método de pago, para que pueda completar mi compra usando mi opción preferida.

#### Acceptance Criteria

1. THE Checkout_System SHALL mostrar las opciones de métodos de pago disponibles (tarjeta de crédito, PayPal, transferencia, etc.)
2. WHEN el User selecciona un Payment_Method, THE Checkout_System SHALL almacenar la selección en el Checkout_Store
3. THE Checkout_System SHALL requerir que un Payment_Method sea seleccionado antes de proceder al checkout
4. WHEN no hay Payment_Method seleccionado, THE Checkout_System SHALL mostrar un mensaje de validación

### Requirement 8: Resumen del Pedido

**User Story:** Como usuario, quiero ver un resumen completo de mi pedido, para que pueda verificar toda la información antes de confirmar.

#### Acceptance Criteria

1. THE Checkout_System SHALL mostrar un resumen con todos los items del carrito incluyendo nombre, cantidad, precio unitario y subtotal
2. THE Checkout_System SHALL mostrar el subtotal de productos, descuentos aplicados, costo de envío y total final
3. WHEN se aplica un cupón, THE Checkout_System SHALL mostrar el descuento en el resumen
4. THE Checkout_System SHALL actualizar el resumen en tiempo real cuando cambian los métodos de envío o se aplican cupones

### Requirement 9: Validación y Envío del Checkout

**User Story:** Como usuario, quiero que el sistema valide toda mi información antes de enviar el pedido, para que no ocurran errores en el procesamiento.

#### Acceptance Criteria

1. WHEN el User hace clic en "Realizar Pedido", THE Checkout_System SHALL validar que la Shipping_Address esté completa
2. WHEN el User hace clic en "Realizar Pedido", THE Checkout_System SHALL validar que la Billing_Address esté completa
3. WHEN el User hace clic en "Realizar Pedido", THE Checkout_System SHALL validar que un Shipping_Method esté seleccionado
4. WHEN el User hace clic en "Realizar Pedido", THE Checkout_System SHALL validar que un Payment_Method esté seleccionado
5. WHEN todas las validaciones pasan, THE Checkout_System SHALL enviar un POST a /api/cart/checkout con el payload completo
6. WHEN alguna validación falla, THE Checkout_System SHALL mostrar mensajes de error específicos y prevenir el envío

### Requirement 10: Procesamiento del Pedido

**User Story:** Como usuario, quiero que mi pedido sea procesado correctamente, para que reciba confirmación de mi compra.

#### Acceptance Criteria

1. WHEN se envía el checkout, THE Checkout_System SHALL incluir shipping_address, billing_address, shipping_method, payment_method y notes en el payload
2. WHEN el backend procesa exitosamente el pedido, THE Checkout_System SHALL recibir un OrderResource con los datos del pedido
3. WHEN el pedido es exitoso, THE Checkout_System SHALL redirigir al User a la página de confirmación
4. WHEN ocurre un error en el backend, THE Checkout_System SHALL mostrar el mensaje de error devuelto por la Cart_API
5. WHILE se procesa el pedido, THE Checkout_System SHALL mostrar un indicador de carga y deshabilitar el botón de envío

### Requirement 11: Página de Confirmación del Pedido

**User Story:** Como usuario, quiero ver la confirmación de mi pedido, para que tenga certeza de que mi compra fue exitosa.

#### Acceptance Criteria

1. THE Checkout_System SHALL mostrar una página de confirmación con el número de orden
2. THE Checkout_System SHALL mostrar el resumen de items comprados con cantidades y precios
3. THE Checkout_System SHALL mostrar las direcciones de envío y facturación confirmadas
4. THE Checkout_System SHALL mostrar el método de envío y pago seleccionados
5. THE Checkout_System SHALL proporcionar un botón "Volver al inicio" que redirija a la página principal

### Requirement 12: Manejo de Errores y Feedback

**User Story:** Como usuario, quiero recibir feedback claro sobre el estado de mis acciones, para que sepa si algo salió mal o si todo está correcto.

#### Acceptance Criteria

1. WHEN ocurre un error de validación, THE Checkout_System SHALL mostrar mensajes de error junto a los campos correspondientes
2. WHEN ocurre un error del backend, THE Checkout_System SHALL mostrar un toast o alerta con el mensaje de error
3. WHEN una acción es exitosa, THE Checkout_System SHALL mostrar un feedback visual positivo
4. THE Checkout_System SHALL mantener los datos ingresados por el User cuando ocurre un error, para que no tenga que volver a ingresarlos

### Requirement 13: Diseño y Experiencia de Usuario

**User Story:** Como usuario, quiero una interfaz clara y fácil de usar, para que pueda completar mi compra sin confusión.

#### Acceptance Criteria

1. THE Checkout_System SHALL usar TailwindCSS para el diseño visual
2. THE Checkout_System SHALL organizar el checkout en secciones claramente diferenciadas
3. THE Checkout_System SHALL usar indicadores visuales para mostrar el progreso del checkout
4. THE Checkout_System SHALL ser responsive y funcionar correctamente en dispositivos móviles y desktop
5. THE Checkout_System SHALL usar componentes reutilizables para mantener consistencia visual

### Requirement 14: Arquitectura de Componentes

**User Story:** Como desarrollador, quiero una arquitectura modular de componentes, para que el código sea mantenible y reutilizable.

#### Acceptance Criteria

1. THE Checkout_System SHALL implementar componentes separados para ShippingAddressForm, BillingAddressForm, ShippingMethods, PaymentMethods y OrderSummary
2. THE Checkout_System SHALL usar una vista principal CheckoutPage.vue que orqueste todos los componentes
3. THE Checkout_System SHALL implementar una vista OrderSuccess.vue para la confirmación del pedido
4. WHEN se modifica un componente, THE Checkout_System SHALL mantener la funcionalidad de otros componentes sin cambios
5. THE Checkout_System SHALL seguir principios SOLID en el backend y buenas prácticas de Vue 3 en el frontend
