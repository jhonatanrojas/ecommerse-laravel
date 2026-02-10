# Requirements Document

## Introduction

Este documento define los requisitos funcionales para implementar la lógica completa del carrito de compra en un proyecto Laravel existente. El sistema debe soportar carritos persistentes para usuarios autenticados, carritos temporales para invitados, gestión de cupones, validaciones de stock, y un proceso de checkout transaccional que genere órdenes de compra.

## Glossary

- **Cart**: Carrito de compra que contiene items y puede estar asociado a un usuario autenticado o a una sesión de invitado
- **Cart_Item**: Ítem individual dentro del carrito que referencia un producto o variante con cantidad y precio
- **Product**: Producto base disponible para compra
- **Product_Variant**: Variante específica de un producto (ej: talla, color)
- **Coupon**: Código promocional que aplica descuentos al carrito
- **Order**: Pedido generado desde un carrito durante el checkout
- **Order_Item**: Ítem individual dentro de una orden con snapshot de datos del producto
- **Guest_Cart**: Carrito temporal asociado a session_id para usuarios no autenticados
- **Authenticated_Cart**: Carrito persistente asociado a user_id para usuarios autenticados
- **Checkout**: Proceso de conversión del carrito en una orden de compra
- **Stock**: Cantidad disponible de un producto o variante
- **Subtotal**: Suma de (price × quantity) de todos los items del carrito
- **Discount**: Monto de descuento aplicado por cupón
- **Tax**: Impuesto calculado sobre el subtotal
- **Shipping_Cost**: Costo de envío estimado
- **Total**: Monto final (subtotal - discount + tax + shipping_cost)
- **Cart_Service**: Servicio que encapsula la lógica de negocio del carrito
- **Overselling**: Venta de más unidades de las disponibles en stock

## Requirements

### Requirement 1: Cart Persistence

**User Story:** Como usuario del sistema, quiero que mi carrito se mantenga persistente según mi estado de autenticación, para que no pierda mis items seleccionados.

#### Acceptance Criteria

1. WHEN un usuario autenticado añade items al carrito, THE Cart_Service SHALL crear o recuperar un carrito asociado a su user_id
2. WHEN un usuario invitado añade items al carrito, THE Cart_Service SHALL crear o recuperar un carrito asociado a su session_id
3. WHEN un usuario invitado inicia sesión, THE Cart_Service SHALL migrar automáticamente los items de su carrito temporal al carrito del usuario autenticado
4. WHEN un carrito temporal excede su fecha de expiración (expires_at), THE System SHALL marcarlo como expirado y no permitir operaciones sobre él
5. WHEN se migra un carrito de invitado a usuario autenticado, THE Cart_Service SHALL consolidar items duplicados sumando cantidades

### Requirement 2: Cart CRUD Operations

**User Story:** Como usuario, quiero realizar operaciones básicas sobre mi carrito, para gestionar los productos que deseo comprar.

#### Acceptance Criteria

1. WHEN un usuario añade el primer ítem, THE Cart_Service SHALL crear automáticamente un nuevo carrito con UUID único
2. WHEN un usuario añade un producto o variante al carrito, THE Cart_Service SHALL crear un Cart_Item con product_id o product_variant_id, quantity y price actual
3. WHEN un usuario actualiza la cantidad de un ítem existente, THE Cart_Service SHALL modificar el quantity del Cart_Item correspondiente
4. WHEN un usuario elimina un ítem individual, THE Cart_Service SHALL remover el Cart_Item del carrito
5. WHEN un usuario vacía el carrito completo, THE Cart_Service SHALL eliminar todos los Cart_Items asociados
6. WHEN un usuario solicita el resumen del carrito, THE Cart_Service SHALL retornar todos los items con subtotal, discount, tax, shipping_cost y total calculados

### Requirement 3: Stock and Product Validation

**User Story:** Como administrador del sistema, quiero que se validen productos y stock antes de operaciones del carrito, para evitar ventas de productos no disponibles.

#### Acceptance Criteria

1. WHEN se añade un producto al carrito, THE Cart_Service SHALL verificar que el Product existe y su campo is_active es true
2. WHEN se añade una variante al carrito, THE Cart_Service SHALL verificar que el Product_Variant existe y su Product padre tiene is_active true
3. WHEN se añade o actualiza un ítem, THE Cart_Service SHALL validar que la cantidad solicitada no exceda el stock disponible
4. WHEN se añade o actualiza un ítem, THE Cart_Service SHALL obtener el precio actual desde la base de datos y no confiar en el precio enviado por el cliente
5. WHEN se añade un ítem que excede el stock disponible, THE Cart_Service SHALL rechazar la operación y retornar un error descriptivo
6. WHEN se añade un producto inactivo, THE Cart_Service SHALL rechazar la operación y retornar un error descriptivo

### Requirement 4: Cart Totals Calculation

**User Story:** Como usuario, quiero ver los totales calculados correctamente en mi carrito, para conocer el monto exacto a pagar.

#### Acceptance Criteria

1. THE Cart_Service SHALL calcular el subtotal como la suma de (price × quantity) de todos los Cart_Items
2. WHEN existe un cupón aplicado, THE Cart_Service SHALL calcular el discount según el tipo y valor del Coupon
3. THE Cart_Service SHALL calcular el tax aplicando el porcentaje de impuesto configurado sobre el subtotal después del descuento
4. THE Cart_Service SHALL incluir el shipping_cost en el cálculo del total
5. THE Cart_Service SHALL calcular el total como (subtotal - discount + tax + shipping_cost)
6. WHEN cambia cualquier ítem del carrito, THE Cart_Service SHALL recalcular automáticamente todos los totales

### Requirement 5: Coupon Management

**User Story:** Como usuario, quiero aplicar cupones de descuento a mi carrito, para obtener beneficios promocionales.

#### Acceptance Criteria

1. WHEN un usuario aplica un código de cupón, THE Cart_Service SHALL verificar que el Coupon existe y su campo is_active es true
2. WHEN se aplica un cupón, THE Cart_Service SHALL validar que la fecha actual está entre starts_at y expires_at
3. WHEN se aplica un cupón, THE Cart_Service SHALL validar que used_count no ha alcanzado el usage_limit
4. WHEN se aplica un cupón con usage_limit_per_user, THE Cart_Service SHALL validar que el usuario no ha excedido su límite personal de uso
5. WHEN se aplica un cupón con min_purchase_amount, THE Cart_Service SHALL validar que el subtotal del carrito cumple el mínimo requerido
6. WHEN un cupón es de tipo "fixed", THE Cart_Service SHALL aplicar el descuento como monto fijo en la moneda del sistema
7. WHEN un cupón es de tipo "percentage", THE Cart_Service SHALL aplicar el descuento como porcentaje del subtotal
8. WHEN un cupón tiene max_discount_amount, THE Cart_Service SHALL limitar el descuento calculado a ese máximo
9. WHEN se aplica un cupón válido, THE Cart_Service SHALL almacenar el coupon_code y discount_amount en el Cart
10. WHEN un usuario remueve un cupón, THE Cart_Service SHALL limpiar coupon_code y discount_amount del Cart y recalcular totales

### Requirement 6: Concurrency and Stock Reservation

**User Story:** Como administrador del sistema, quiero prevenir overselling mediante control de concurrencia, para mantener la integridad del inventario.

#### Acceptance Criteria

1. WHEN se añade o actualiza un ítem del carrito, THE Cart_Service SHALL usar transacciones de base de datos para garantizar atomicidad
2. WHEN múltiples usuarios intentan añadir el mismo producto simultáneamente, THE Cart_Service SHALL usar bloqueo pesimista (lockForUpdate) para prevenir race conditions
3. WHEN se valida stock durante operaciones del carrito, THE Cart_Service SHALL consultar el stock en tiempo real dentro de la transacción
4. WHEN falla cualquier validación dentro de una transacción, THE Cart_Service SHALL hacer rollback completo de la operación

### Requirement 7: Checkout Process

**User Story:** Como usuario, quiero completar la compra de mi carrito, para generar una orden de compra confirmada.

#### Acceptance Criteria

1. WHEN un usuario inicia checkout, THE Cart_Service SHALL crear un Order con order_number único, user_id, status, payment_status, y todos los totales del carrito
2. WHEN se crea la orden, THE Cart_Service SHALL generar Order_Items copiando product_id, product_variant_id, product_name, product_sku, quantity, price, subtotal, tax y total de cada Cart_Item
3. WHEN se crea la orden, THE Cart_Service SHALL decrementar el stock del Product o Product_Variant por la cantidad vendida
4. WHEN se usó un cupón, THE Cart_Service SHALL incrementar el used_count del Coupon y copiar el coupon_code al Order
5. WHEN el checkout es exitoso, THE Cart_Service SHALL vaciar todos los Cart_Items del carrito
6. WHEN falla cualquier paso del checkout, THE Cart_Service SHALL hacer rollback completo sin crear Order ni modificar stock
7. THE Cart_Service SHALL ejecutar todo el proceso de checkout dentro de una única transacción de base de datos

### Requirement 8: Events and Side Effects

**User Story:** Como desarrollador del sistema, quiero emitir eventos durante operaciones del carrito, para permitir side effects desacoplados como emails y analytics.

#### Acceptance Criteria

1. WHEN se crea un nuevo carrito, THE Cart_Service SHALL emitir el evento CartCreated
2. WHEN se añade un ítem al carrito, THE Cart_Service SHALL emitir el evento CartItemAdded
3. WHEN se actualiza un ítem del carrito, THE Cart_Service SHALL emitir el evento CartItemUpdated
4. WHEN se elimina un ítem del carrito, THE Cart_Service SHALL emitir el evento CartItemRemoved
5. WHEN se vacía el carrito, THE Cart_Service SHALL emitir el evento CartCleared
6. WHEN se aplica un cupón, THE Cart_Service SHALL emitir el evento CouponApplied
7. WHEN se remueve un cupón, THE Cart_Service SHALL emitir el evento CouponRemoved
8. WHEN se completa el checkout, THE Cart_Service SHALL emitir el evento CartCheckedOut con los datos del Order creado

### Requirement 9: Security and Authorization

**User Story:** Como administrador del sistema, quiero garantizar la seguridad de las operaciones del carrito, para prevenir manipulación maliciosa y accesos no autorizados.

#### Acceptance Criteria

1. WHEN un usuario autenticado opera sobre un carrito, THE Cart_Service SHALL verificar que el cart.user_id coincide con el usuario actual
2. WHEN un usuario invitado opera sobre un carrito, THE Cart_Service SHALL verificar que el cart.session_id coincide con la sesión actual
3. WHEN se añade o actualiza un ítem, THE Cart_Service SHALL obtener el precio desde la base de datos y rechazar precios enviados por el cliente
4. THE Cart_Service SHALL sanitizar todas las entradas de usuario antes de procesarlas
5. WHEN se detecta un intento de manipulación de precios, THE Cart_Service SHALL registrar el evento en logs y rechazar la operación

### Requirement 10: Performance Optimization

**User Story:** Como usuario del sistema, quiero que las operaciones del carrito sean rápidas, para tener una experiencia fluida.

#### Acceptance Criteria

1. WHEN se carga un carrito con sus items, THE Cart_Service SHALL usar eager loading para cargar relaciones de products y product_variants
2. WHEN se calculan totales del carrito, THE Cart_Service SHALL minimizar queries a base de datos cargando datos necesarios en una sola consulta
3. THE Cart_Service SHALL usar índices de base de datos apropiados en user_id, session_id y uuid para búsquedas rápidas
4. WHEN se consulta el carrito frecuentemente, THE Cart_Service SHALL considerar cachear el resultado por un tiempo corto

### Requirement 11: Audit and Logging

**User Story:** Como administrador del sistema, quiero registrar eventos importantes del carrito, para auditoría y debugging.

#### Acceptance Criteria

1. WHEN se aplica un cupón, THE Cart_Service SHALL registrar en logs el user_id o session_id, coupon_code, y discount_amount aplicado
2. WHEN se completa un checkout, THE Cart_Service SHALL registrar en logs el cart_id, order_id, user_id, y total de la orden
3. WHEN se detecta stock insuficiente, THE Cart_Service SHALL registrar en logs el product_id o product_variant_id, stock disponible, y cantidad solicitada
4. WHEN se detecta intento de manipulación de precios, THE Cart_Service SHALL registrar en logs el user_id o session_id, product_id, precio esperado, y precio recibido
5. WHEN se migra un carrito de invitado a usuario, THE Cart_Service SHALL registrar en logs el session_id origen, user_id destino, y cantidad de items migrados

### Requirement 12: Cart Expiration Management

**User Story:** Como administrador del sistema, quiero que los carritos temporales expiren automáticamente, para mantener la base de datos limpia.

#### Acceptance Criteria

1. WHEN se crea un carrito temporal (guest), THE Cart_Service SHALL establecer expires_at a 30 días desde la creación
2. WHEN se crea un carrito autenticado, THE Cart_Service SHALL dejar expires_at como null
3. WHEN se intenta operar sobre un carrito expirado, THE Cart_Service SHALL rechazar la operación y retornar un error indicando expiración
4. THE System SHALL proporcionar un comando o job para limpiar carritos expirados periódicamente

### Requirement 13: Cart Item Quantity Limits

**User Story:** Como administrador del sistema, quiero establecer límites de cantidad por producto, para prevenir compras excesivas.

#### Acceptance Criteria

1. WHEN se añade o actualiza un ítem, THE Cart_Service SHALL validar que la cantidad no exceda un límite máximo configurable por ítem (ej: 99 unidades)
2. WHEN se añade o actualiza un ítem con cantidad menor a 1, THE Cart_Service SHALL rechazar la operación
3. WHEN se intenta añadir cantidad que excede el límite, THE Cart_Service SHALL retornar un error descriptivo con el límite permitido

### Requirement 14: Price Snapshot

**User Story:** Como usuario, quiero que los precios en mi orden reflejen los precios al momento de la compra, para evitar discrepancias.

#### Acceptance Criteria

1. WHEN se crea un Order_Item durante checkout, THE Cart_Service SHALL copiar el precio actual del Product o Product_Variant al campo price del Order_Item
2. WHEN cambia el precio de un producto después del checkout, THE Order_Item SHALL mantener el precio histórico al momento de la compra
3. THE Cart_Service SHALL almacenar product_name y product_sku en Order_Item para mantener snapshot completo del producto
