# Design Document: Shopping Cart Logic

## Overview

Este documento describe el diseño técnico para implementar la lógica completa del carrito de compra en un proyecto Laravel existente. El sistema soportará carritos persistentes para usuarios autenticados, carritos temporales para invitados con migración automática, gestión de cupones con validaciones complejas, y un proceso de checkout transaccional que garantice la integridad del inventario.

El diseño sigue los principios SOLID y utiliza el patrón de servicios de Laravel para encapsular la lógica de negocio, manteniendo los controladores delgados. Se implementarán eventos para side effects desacoplados, transacciones de base de datos para operaciones críticas, y bloqueos para prevenir race conditions.

## Architecture

### Architectural Pattern

El sistema seguirá una arquitectura en capas basada en el patrón MVC de Laravel, con las siguientes capas adicionales:

```
┌─────────────────────────────────────────┐
│         Controllers Layer               │
│  (HTTP Request Handling, Validation)    │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Services Layer                  │
│  (Business Logic, Orchestration)        │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Models Layer                    │
│  (Data Access, Relationships)           │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Database Layer                  │
│  (MySQL with Transactions & Locks)      │
└─────────────────────────────────────────┘

         Events & Listeners
              (Side Effects)
```

### Service Layer Design

El servicio principal `CartService` orquestará todas las operaciones del carrito. Este servicio será inyectado en controladores mediante dependency injection de Laravel.

**Responsabilidades del CartService:**
- Gestión del ciclo de vida del carrito (crear, recuperar, expirar)
- Operaciones CRUD sobre items del carrito
- Validaciones de productos, variantes y stock
- Cálculo de totales (subtotal, descuentos, impuestos, envío)
- Gestión de cupones (aplicar, validar, remover)
- Migración de carritos de invitado a usuario autenticado
- Proceso de checkout transaccional
- Emisión de eventos para side effects

### Transaction Management

Las operaciones críticas se ejecutarán dentro de transacciones de base de datos:

**Operaciones transaccionales:**
- Añadir/actualizar items con validación de stock
- Aplicar cupones con validación de límites
- Checkout completo (crear orden, decrementar stock, vaciar carrito)
- Migración de carrito de invitado a usuario

**Estrategia de bloqueo:**
- Bloqueo pesimista (`lockForUpdate`) para validación de stock en operaciones concurrentes
- Bloqueo pesimista para incrementar `used_count` de cupones
- Rollback automático si falla cualquier validación

## Components and Interfaces

### CartService

Servicio principal que encapsula toda la lógica de negocio del carrito.

```php
namespace App\Services\Cart;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository,
        private CouponValidator $couponValidator,
        private StockValidator $stockValidator,
        private CartCalculator $cartCalculator
    ) {}

    // Cart Lifecycle
    public function getOrCreateCart(?User $user, ?string $sessionId): Cart;
    public function findCart(?User $user, ?string $sessionId): ?Cart;
    public function isCartExpired(Cart $cart): bool;
    public function migrateGuestCartToUser(string $sessionId, User $user): Cart;

    // Cart Items CRUD
    public function addItem(Cart $cart, int $productId, ?int $variantId, int $quantity): CartItem;
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem;
    public function removeItem(CartItem $item): void;
    public function clearCart(Cart $cart): void;

    // Cart Summary
    public function getCartSummary(Cart $cart): CartSummary;
    public function recalculateTotals(Cart $cart): CartSummary;

    // Coupon Management
    public function applyCoupon(Cart $cart, string $couponCode, ?User $user): Cart;
    public function removeCoupon(Cart $cart): Cart;

    // Checkout
    public function checkout(Cart $cart, CheckoutData $data): Order;

    // Authorization
    public function authorizeCartAccess(Cart $cart, ?User $user, ?string $sessionId): bool;
}
```

### CartRepository

Repositorio para acceso a datos del carrito (opcional, puede usar directamente Eloquent).

```php
namespace App\Services\Cart;

class CartRepository
{
    public function findByUser(User $user): ?Cart;
    public function findBySession(string $sessionId): ?Cart;
    public function findByUuid(string $uuid): ?Cart;
    public function create(array $data): Cart;
    public function update(Cart $cart, array $data): Cart;
    public function deleteExpiredCarts(): int;
}
```

### CouponValidator

Validador especializado para cupones con todas las reglas de negocio.

```php
namespace App\Services\Cart;

class CouponValidator
{
    public function validate(Coupon $coupon, Cart $cart, ?User $user): ValidationResult;
    public function isActive(Coupon $coupon): bool;
    public function isWithinDateRange(Coupon $coupon): bool;
    public function hasReachedUsageLimit(Coupon $coupon): bool;
    public function hasUserReachedLimit(Coupon $coupon, User $user): bool;
    public function meetsMinimumPurchase(Coupon $coupon, float $subtotal): bool;
}
```

### StockValidator

Validador especializado para stock y disponibilidad de productos.

```php
namespace App\Services\Cart;

class StockValidator
{
    public function validateProduct(int $productId): ValidationResult;
    public function validateVariant(int $variantId): ValidationResult;
    public function validateStock(int $productId, ?int $variantId, int $quantity): ValidationResult;
    public function getAvailableStock(int $productId, ?int $variantId): int;
    public function isProductActive(Product $product): bool;
}
```

### CartCalculator

Calculadora para todos los totales del carrito.

```php
namespace App\Services\Cart;

class CartCalculator
{
    public function calculateSubtotal(Cart $cart): float;
    public function calculateDiscount(Cart $cart, Coupon $coupon): float;
    public function calculateTax(float $subtotalAfterDiscount): float;
    public function calculateShipping(Cart $cart): float;
    public function calculateTotal(Cart $cart): float;
    public function getTaxRate(): float;
}
```

### CheckoutService

Servicio especializado para el proceso de checkout (puede ser parte de CartService o separado).

```php
namespace App\Services\Cart;

class CheckoutService
{
    public function __construct(
        private OrderService $orderService,
        private StockManager $stockManager,
        private CouponManager $couponManager
    ) {}

    public function processCheckout(Cart $cart, CheckoutData $data): Order;
    private function createOrder(Cart $cart, CheckoutData $data): Order;
    private function createOrderItems(Order $order, Cart $cart): void;
    private function decrementStock(Cart $cart): void;
    private function incrementCouponUsage(Cart $cart, ?User $user): void;
    private function clearCart(Cart $cart): void;
}
```

### Data Transfer Objects

```php
namespace App\Services\Cart\DTOs;

class CartSummary
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $tax,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly int $itemCount,
        public readonly ?string $couponCode
    ) {}
}

class CheckoutData
{
    public function __construct(
        public readonly ?int $shippingAddressId,
        public readonly ?int $billingAddressId,
        public readonly string $paymentMethod,
        public readonly string $shippingMethod,
        public readonly ?string $customerNotes
    ) {}
}

class ValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $errorMessage = null
    ) {}

    public static function success(): self;
    public static function fail(string $message): self;
}
```

### Events

```php
namespace App\Events\Cart;

// Cart Events
class CartCreated { public Cart $cart; }
class CartItemAdded { public Cart $cart; public CartItem $item; }
class CartItemUpdated { public Cart $cart; public CartItem $item; public int $oldQuantity; }
class CartItemRemoved { public Cart $cart; public CartItem $item; }
class CartCleared { public Cart $cart; }
class CartMigrated { public Cart $guestCart; public Cart $userCart; public User $user; }

// Coupon Events
class CouponApplied { public Cart $cart; public Coupon $coupon; public float $discount; }
class CouponRemoved { public Cart $cart; public string $couponCode; }

// Checkout Events
class CartCheckedOut { public Cart $cart; public Order $order; }
class CheckoutFailed { public Cart $cart; public string $reason; }
```

### Exceptions

```php
namespace App\Exceptions\Cart;

class CartException extends Exception {}
class CartExpiredException extends CartException {}
class CartNotFoundException extends CartException {}
class UnauthorizedCartAccessException extends CartException {}

class CartItemException extends Exception {}
class ProductNotFoundException extends CartItemException {}
class ProductInactiveException extends CartItemException {}
class InsufficientStockException extends CartItemException {}
class InvalidQuantityException extends CartItemException {}

class CouponException extends Exception {}
class CouponNotFoundException extends CouponException {}
class CouponInactiveException extends CouponException {}
class CouponExpiredException extends CouponException {}
class CouponUsageLimitException extends CouponException {}
class MinimumPurchaseNotMetException extends CouponException {}

class CheckoutException extends Exception {}
class CheckoutValidationException extends CheckoutException {}
```

## Data Models

### Existing Models

Los modelos ya existen en el proyecto. Aquí se documentan las relaciones y métodos relevantes:

**Cart Model:**
- Relaciones: `user()`, `items()`
- Campos clave: `user_id`, `session_id`, `coupon_code`, `discount_amount`, `expires_at`
- UUID como route key

**CartItem Model:**
- Relaciones: `cart()`, `product()`, `variant()`
- Campos clave: `product_id`, `product_variant_id`, `quantity`, `price`
- UUID como route key

**Product Model:**
- Relaciones: `category()`, `images()`, `variants()`, `reviews()`
- Campos clave: `name`, `sku`, `price`, `stock`, `is_active`
- Scopes: `active()`, `inStock()`
- Atributos computados: `is_low_stock`, `is_out_of_stock`

**ProductVariant Model:**
- Relaciones: `product()`
- Campos clave: `product_id`, `name`, `sku`, `price`, `stock`
- Soft deletes habilitado

**Coupon Model:**
- Relaciones: `users()` (many-to-many con pivot `coupon_user`)
- Campos clave: `code`, `type` (enum), `value`, `min_purchase_amount`, `max_discount_amount`, `usage_limit`, `used_count`, `usage_limit_per_user`, `starts_at`, `expires_at`, `is_active`
- Enum `CouponType`: `Fixed`, `Percentage`

**Order Model:**
- Relaciones: `user()`, `items()`, `shippingAddress()`, `billingAddress()`, `payments()`
- Campos clave: `order_number`, `status`, `payment_status`, `subtotal`, `discount`, `tax`, `shipping_cost`, `total`, `coupon_code`
- Enums: `OrderStatus`, `PaymentStatus`

**OrderItem Model:**
- Relaciones: `order()`, `product()`, `variant()`
- Campos clave: `product_id`, `product_variant_id`, `product_name`, `product_sku`, `quantity`, `price`, `subtotal`, `tax`, `total`
- Snapshot de datos del producto para historial

### Database Indexes

Los índices necesarios para rendimiento óptimo:

```sql
-- Carts table
INDEX idx_carts_user_id (user_id)
INDEX idx_carts_session_id (session_id)
INDEX idx_carts_expires_at (expires_at)
INDEX idx_carts_uuid (uuid) -- Ya existe por HasUuids

-- Cart Items table
INDEX idx_cart_items_cart_id (cart_id)
INDEX idx_cart_items_product_id (product_id)
INDEX idx_cart_items_variant_id (product_variant_id)

-- Coupons table
INDEX idx_coupons_code (code)
INDEX idx_coupons_is_active (is_active)
INDEX idx_coupons_expires_at (expires_at)

-- Products table
INDEX idx_products_is_active (is_active)
INDEX idx_products_sku (sku)
```

## Correctness Properties

*Una propiedad es una característica o comportamiento que debe mantenerse verdadero en todas las ejecuciones válidas de un sistema—esencialmente, una declaración formal sobre lo que el sistema debe hacer. Las propiedades sirven como puente entre las especificaciones legibles por humanos y las garantías de correctitud verificables por máquina.*


### Cart Persistence Properties

**Property 1: Authenticated cart association**
*For any* authenticated user adding items to a cart, the cart should be associated with that user's user_id and retrievable by that user_id.
**Validates: Requirements 1.1**

**Property 2: Guest cart association**
*For any* guest user adding items to a cart, the cart should be associated with that session's session_id and retrievable by that session_id.
**Validates: Requirements 1.2**

**Property 3: Cart migration preserves items**
*For any* guest cart being migrated to an authenticated user, all items from the guest cart should be present in the user's cart after migration.
**Validates: Requirements 1.3**

**Property 4: Cart expiration enforcement**
*For any* cart with expires_at in the past, any operation on that cart should be rejected with an expiration error.
**Validates: Requirements 1.4, 12.3**

**Property 5: Item consolidation during migration**
*For any* guest cart and user cart containing the same product/variant, after migration the quantity should equal the sum of both quantities.
**Validates: Requirements 1.5**

### Cart CRUD Properties

**Property 6: Automatic cart creation**
*For any* user without an existing cart, adding an item should automatically create a new cart with a unique UUID.
**Validates: Requirements 2.1**

**Property 7: Cart item structure completeness**
*For any* product or variant added to a cart, the created CartItem should contain product_id (or product_variant_id), quantity, and the current price from the database.
**Validates: Requirements 2.2**

**Property 8: Quantity update reflection**
*For any* cart item being updated with a new quantity, the item's quantity field should reflect the new value after the operation.
**Validates: Requirements 2.3**

**Property 9: Item removal completeness**
*For any* cart item being removed, that item should not be present in the cart after the removal operation.
**Validates: Requirements 2.4**

**Property 10: Cart clearing completeness**
*For any* cart being cleared, the cart should contain zero items after the clear operation.
**Validates: Requirements 2.5**

**Property 11: Cart summary structure**
*For any* cart summary request, the response should contain subtotal, discount, tax, shipping_cost, total, item_count, and coupon_code fields.
**Validates: Requirements 2.6**

### Product and Stock Validation Properties

**Property 12: Active product validation**
*For any* product being added to a cart, if the product does not exist or is_active is false, the operation should be rejected.
**Validates: Requirements 3.1**

**Property 13: Active variant validation**
*For any* variant being added to a cart, if the variant does not exist or its parent product's is_active is false, the operation should be rejected.
**Validates: Requirements 3.2**

**Property 14: Stock availability validation**
*For any* item being added or updated in a cart, if the requested quantity exceeds available stock, the operation should be rejected.
**Validates: Requirements 3.3**

**Property 15: Price integrity enforcement**
*For any* item added or updated in a cart, the price stored in the CartItem should match the current price in the database, regardless of any price sent by the client.
**Validates: Requirements 3.4, 9.3**

### Cart Totals Calculation Properties

**Property 16: Subtotal calculation correctness**
*For any* cart, the subtotal should equal the sum of (price × quantity) for all cart items.
**Validates: Requirements 4.1**

**Property 17: Fixed coupon discount calculation**
*For any* cart with a fixed-type coupon applied, the discount should equal the coupon's value (or the subtotal if subtotal is less than the coupon value).
**Validates: Requirements 5.6**

**Property 18: Percentage coupon discount calculation**
*For any* cart with a percentage-type coupon applied, the discount should equal subtotal × (coupon_value / 100), capped at max_discount_amount if defined.
**Validates: Requirements 4.2, 5.7, 5.8**

**Property 19: Tax calculation correctness**
*For any* cart, the tax should equal (subtotal - discount) × tax_rate.
**Validates: Requirements 4.3**

**Property 20: Total calculation correctness**
*For any* cart, the total should equal (subtotal - discount + tax + shipping_cost).
**Validates: Requirements 4.5**

**Property 21: Totals recalculation on change**
*For any* cart where an item is added, updated, or removed, the totals (subtotal, discount, tax, total) should reflect the current state of the cart.
**Validates: Requirements 4.6**

### Coupon Validation Properties

**Property 22: Comprehensive coupon validation**
*For any* coupon being applied to a cart, the coupon should only be accepted if all of the following are true:
- The coupon exists and is_active is true
- Current date is between starts_at and expires_at
- used_count has not reached usage_limit
- User has not exceeded usage_limit_per_user (if applicable)
- Cart subtotal meets min_purchase_amount (if defined)
**Validates: Requirements 5.1, 5.2, 5.3, 5.4, 5.5**

**Property 23: Coupon persistence after application**
*For any* valid coupon applied to a cart, the cart's coupon_code and discount_amount fields should be populated with the coupon's code and calculated discount.
**Validates: Requirements 5.9**

**Property 24: Coupon removal completeness**
*For any* cart with a coupon being removed, the cart's coupon_code and discount_amount should be null/zero, and totals should be recalculated without the discount.
**Validates: Requirements 5.10**

### Concurrency and Atomicity Properties

**Property 25: No overselling under concurrency**
*For any* product with stock S, if multiple concurrent operations attempt to add quantities Q1, Q2, ..., Qn to different carts, the total quantity successfully added across all carts should not exceed S.
**Validates: Requirements 6.2**

**Property 26: Transaction rollback on validation failure**
*For any* cart operation that fails validation (stock, coupon, etc.), the database state should be identical to before the operation was attempted.
**Validates: Requirements 6.4**

### Checkout Properties

**Property 27: Order creation completeness**
*For any* cart being checked out, a new Order should be created with a unique order_number, the cart's user_id, and all totals (subtotal, discount, tax, shipping_cost, total) matching the cart's calculated totals.
**Validates: Requirements 7.1**

**Property 28: Order items snapshot accuracy**
*For any* cart being checked out, each CartItem should generate an OrderItem with matching product_id, product_variant_id, product_name, product_sku, quantity, price, subtotal, tax, and total.
**Validates: Requirements 7.2, 14.1, 14.3**

**Property 29: Stock decrement on checkout**
*For any* product or variant in a cart being checked out with quantity Q, the product/variant's stock should decrease by Q after successful checkout.
**Validates: Requirements 7.3**

**Property 30: Coupon usage increment on checkout**
*For any* cart with a coupon being checked out, the coupon's used_count should increase by 1, and the order should contain the coupon_code.
**Validates: Requirements 7.4**

**Property 31: Cart clearing after checkout**
*For any* cart successfully checked out, the cart should contain zero items after the checkout completes.
**Validates: Requirements 7.5**

**Property 32: Checkout atomicity**
*For any* checkout operation that fails at any step, no Order should be created, no stock should be decremented, no coupon usage should be incremented, and the cart should remain unchanged.
**Validates: Requirements 7.6**

**Property 33: Order item price immutability**
*For any* OrderItem created during checkout, if the associated Product or ProductVariant's price changes after checkout, the OrderItem's price should remain unchanged.
**Validates: Requirements 14.2**

### Event Emission Properties

**Property 34: Cart lifecycle events emission**
*For any* cart operation (create, add item, update item, remove item, clear, apply coupon, remove coupon, checkout), the corresponding event (CartCreated, CartItemAdded, CartItemUpdated, CartItemRemoved, CartCleared, CouponApplied, CouponRemoved, CartCheckedOut) should be emitted.
**Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8**

### Authorization Properties

**Property 35: Cart ownership validation**
*For any* cart operation by an authenticated user, the operation should only succeed if the cart's user_id matches the current user's id; for guest users, the cart's session_id should match the current session_id.
**Validates: Requirements 9.1, 9.2**

### Cart Expiration Properties

**Property 36: Guest cart expiration setting**
*For any* newly created guest cart, the expires_at field should be set to 30 days from creation time.
**Validates: Requirements 12.1**

**Property 37: Authenticated cart no expiration**
*For any* newly created authenticated cart, the expires_at field should be null.
**Validates: Requirements 12.2**

### Quantity Limits Properties

**Property 38: Maximum quantity validation**
*For any* item being added or updated in a cart, if the quantity exceeds the configured maximum limit (e.g., 99), the operation should be rejected.
**Validates: Requirements 13.1**

**Property 39: Minimum quantity validation**
*For any* item being added or updated in a cart, if the quantity is less than 1, the operation should be rejected.
**Validates: Requirements 13.2**

## Error Handling

### Exception Hierarchy

El sistema utilizará una jerarquía de excepciones personalizada para manejar diferentes tipos de errores:

```
Exception
├── CartException
│   ├── CartExpiredException
│   ├── CartNotFoundException
│   └── UnauthorizedCartAccessException
├── CartItemException
│   ├── ProductNotFoundException
│   ├── ProductInactiveException
│   ├── InsufficientStockException
│   └── InvalidQuantityException
├── CouponException
│   ├── CouponNotFoundException
│   ├── CouponInactiveException
│   ├── CouponExpiredException
│   ├── CouponUsageLimitException
│   └── MinimumPurchaseNotMetException
└── CheckoutException
    └── CheckoutValidationException
```

### Error Response Format

Todas las excepciones del carrito deben retornar respuestas JSON consistentes:

```json
{
  "success": false,
  "message": "Human-readable error message",
  "error_code": "CART_EXPIRED",
  "details": {
    "cart_uuid": "...",
    "expired_at": "2024-01-15T10:30:00Z"
  }
}
```

### Error Handling Strategy

1. **Validation Errors**: Capturar en el servicio y lanzar excepciones específicas
2. **Database Errors**: Envolver en excepciones de dominio apropiadas
3. **Transaction Rollback**: Automático en caso de cualquier excepción
4. **Logging**: Registrar errores críticos (stock insuficiente, manipulación de precios, checkout fallido)
5. **User Feedback**: Mensajes claros y accionables para el usuario

### Critical Error Scenarios

**Stock Insuficiente:**
- Detectar durante validación antes de commit
- Rollback de transacción
- Retornar stock disponible actual
- Log del intento

**Carrito Expirado:**
- Validar expires_at antes de cualquier operación
- Retornar error específico
- Sugerir crear nuevo carrito

**Cupón Inválido:**
- Validar todas las reglas del cupón
- Retornar razón específica del rechazo
- No aplicar descuento parcial

**Checkout Fallido:**
- Rollback completo de la transacción
- Mantener carrito intacto
- Log detallado del error
- Emitir evento CheckoutFailed

## Testing Strategy

### Dual Testing Approach

El sistema implementará tanto pruebas unitarias como pruebas basadas en propiedades (property-based testing) para garantizar correctitud comprehensiva.

**Unit Tests:**
- Casos específicos de validación de productos y variantes
- Ejemplos concretos de cálculos de descuentos
- Casos edge de carritos vacíos
- Manejo de errores específicos (stock insuficiente, cupón expirado)
- Integración entre componentes (CartService → CartRepository)

**Property-Based Tests:**
- Validación de propiedades matemáticas (subtotal, descuentos, impuestos, total)
- Propiedades de persistencia (migración de carritos, consolidación de items)
- Propiedades de atomicidad (rollback en fallos)
- Propiedades de concurrencia (no overselling)
- Propiedades de eventos (emisión correcta)

### Property-Based Testing Configuration

**Framework:** Utilizaremos **Pest PHP** con el plugin **pest-plugin-faker** para property-based testing en Laravel.

**Configuración:**
- Mínimo 100 iteraciones por test de propiedad
- Generadores personalizados para Cart, CartItem, Product, Coupon
- Seeds aleatorios pero reproducibles para debugging

**Tag Format:**
Cada test de propiedad debe incluir un comentario con el formato:
```php
// Feature: shopping-cart-logic, Property 16: Subtotal calculation correctness
```

### Test Organization

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── CartServiceTest.php
│   │   ├── CouponValidatorTest.php
│   │   ├── StockValidatorTest.php
│   │   └── CartCalculatorTest.php
│   └── Models/
│       ├── CartTest.php
│       └── CartItemTest.php
├── Feature/
│   ├── Cart/
│   │   ├── CartCRUDTest.php
│   │   ├── CouponManagementTest.php
│   │   ├── CheckoutTest.php
│   │   └── CartMigrationTest.php
│   └── Properties/
│       ├── CartPersistencePropertiesTest.php
│       ├── CartCalculationPropertiesTest.php
│       ├── CouponValidationPropertiesTest.php
│       ├── CheckoutPropertiesTest.php
│       └── ConcurrencyPropertiesTest.php
└── Integration/
    └── CartCheckoutIntegrationTest.php
```

### Key Test Scenarios

**Cart Persistence Tests:**
- Crear carrito para usuario autenticado
- Crear carrito para invitado
- Migrar carrito de invitado a usuario
- Consolidar items duplicados durante migración
- Rechazar operaciones en carritos expirados

**Cart CRUD Tests:**
- Añadir producto al carrito
- Añadir variante al carrito
- Actualizar cantidad de item
- Eliminar item individual
- Vaciar carrito completo
- Obtener resumen del carrito

**Validation Tests:**
- Rechazar productos inactivos
- Rechazar variantes de productos inactivos
- Rechazar cantidades que exceden stock
- Rechazar cantidades menores a 1
- Rechazar cantidades mayores al límite máximo
- Validar precios desde base de datos

**Coupon Tests:**
- Aplicar cupón fijo válido
- Aplicar cupón porcentual válido
- Rechazar cupón inactivo
- Rechazar cupón expirado
- Rechazar cupón con límite de uso alcanzado
- Rechazar cupón sin monto mínimo
- Aplicar max_discount_amount en cupones porcentuales
- Remover cupón aplicado

**Calculation Tests:**
- Calcular subtotal correctamente
- Calcular descuento fijo correctamente
- Calcular descuento porcentual correctamente
- Calcular impuestos correctamente
- Calcular total correctamente
- Recalcular totales después de cambios

**Checkout Tests:**
- Checkout exitoso crea orden
- Checkout copia items correctamente
- Checkout decrementa stock
- Checkout incrementa uso de cupón
- Checkout vacía carrito
- Checkout falla si stock insuficiente
- Checkout hace rollback en errores

**Concurrency Tests:**
- Múltiples usuarios añadiendo mismo producto
- Prevenir overselling con bloqueos
- Transacciones atómicas en operaciones críticas

**Event Tests:**
- Emitir CartCreated al crear carrito
- Emitir CartItemAdded al añadir item
- Emitir CartItemUpdated al actualizar item
- Emitir CartItemRemoved al eliminar item
- Emitir CartCleared al vaciar carrito
- Emitir CouponApplied al aplicar cupón
- Emitir CouponRemoved al remover cupón
- Emitir CartCheckedOut al completar checkout

### Property Test Examples

**Property 16: Subtotal Calculation**
```php
// Feature: shopping-cart-logic, Property 16: Subtotal calculation correctness
it('calculates subtotal as sum of price × quantity for all items', function () {
    // Generate random cart with random items
    $cart = Cart::factory()->create();
    $items = CartItem::factory()->count(rand(1, 10))->create(['cart_id' => $cart->id]);
    
    $expectedSubtotal = $items->sum(fn($item) => $item->price * $item->quantity);
    $actualSubtotal = app(CartCalculator::class)->calculateSubtotal($cart);
    
    expect($actualSubtotal)->toBe($expectedSubtotal);
})->repeat(100);
```

**Property 25: No Overselling**
```php
// Feature: shopping-cart-logic, Property 25: No overselling under concurrency
it('prevents overselling when multiple users add same product concurrently', function () {
    $product = Product::factory()->create(['stock' => 10]);
    $users = User::factory()->count(5)->create();
    
    // Simulate concurrent additions
    $promises = collect($users)->map(function ($user) use ($product) {
        return async(fn() => app(CartService::class)->addItem(
            app(CartService::class)->getOrCreateCart($user, null),
            $product->id,
            null,
            3
        ));
    });
    
    $results = await($promises);
    $successfulAdditions = collect($results)->filter(fn($r) => $r->isSuccess())->count();
    
    // Maximum 3 users should succeed (10 stock / 3 quantity = 3.33)
    expect($successfulAdditions)->toBeLessThanOrEqual(3);
    expect($product->fresh()->stock)->toBeGreaterThanOrEqual(0);
})->repeat(100);
```

### Test Data Factories

Utilizar factories de Laravel para generar datos de prueba consistentes:

```php
// CartFactory
Cart::factory()->define([
    'user_id' => User::factory(),
    'session_id' => null,
    'coupon_code' => null,
    'discount_amount' => 0,
    'expires_at' => null,
]);

Cart::factory()->guest()->define([
    'user_id' => null,
    'session_id' => Str::random(40),
    'expires_at' => now()->addDays(30),
]);

// CartItemFactory
CartItem::factory()->define([
    'cart_id' => Cart::factory(),
    'product_id' => Product::factory(),
    'product_variant_id' => null,
    'quantity' => fake()->numberBetween(1, 5),
    'price' => fake()->randomFloat(2, 10, 1000),
]);

// CouponFactory
Coupon::factory()->fixed()->define([
    'type' => CouponType::Fixed,
    'value' => fake()->randomFloat(2, 5, 50),
]);

Coupon::factory()->percentage()->define([
    'type' => CouponType::Percentage,
    'value' => fake()->numberBetween(5, 50),
    'max_discount_amount' => fake()->randomFloat(2, 10, 100),
]);
```

### Continuous Integration

Los tests deben ejecutarse automáticamente en CI/CD:
- Ejecutar todos los tests unitarios en cada commit
- Ejecutar tests de propiedades en cada PR
- Ejecutar tests de integración antes de merge a main
- Generar reporte de cobertura (mínimo 80%)
- Fallar el build si algún test falla

## Implementation Notes

### Configuration

Crear archivo de configuración `config/cart.php`:

```php
return [
    'guest_cart_expiration_days' => env('CART_GUEST_EXPIRATION_DAYS', 30),
    'max_item_quantity' => env('CART_MAX_ITEM_QUANTITY', 99),
    'tax_rate' => env('CART_TAX_RATE', 0.16), // 16% IVA
    'default_shipping_cost' => env('CART_DEFAULT_SHIPPING_COST', 0),
    'enable_stock_validation' => env('CART_ENABLE_STOCK_VALIDATION', true),
    'enable_price_validation' => env('CART_ENABLE_PRICE_VALIDATION', true),
];
```

### Service Provider Registration

Registrar servicios en `AppServiceProvider`:

```php
public function register()
{
    $this->app->singleton(CartService::class);
    $this->app->singleton(CartCalculator::class);
    $this->app->singleton(CouponValidator::class);
    $this->app->singleton(StockValidator::class);
}
```

### Event Listeners

Crear listeners para side effects:

```php
// SendCartAbandonmentEmail
// UpdateAnalytics
// SyncInventoryWithWarehouse
// NotifyAdminOfLowStock
// SendOrderConfirmationEmail
```

### Artisan Commands

```php
// php artisan cart:clean-expired
// Limpia carritos expirados de la base de datos

// php artisan cart:migrate-guest {session_id} {user_id}
// Migra manualmente un carrito de invitado a usuario
```

### API Endpoints

```
POST   /api/cart/items                    # Añadir item
PUT    /api/cart/items/{uuid}             # Actualizar cantidad
DELETE /api/cart/items/{uuid}             # Eliminar item
DELETE /api/cart                          # Vaciar carrito
GET    /api/cart                          # Obtener carrito
POST   /api/cart/coupon                   # Aplicar cupón
DELETE /api/cart/coupon                   # Remover cupón
POST   /api/cart/checkout                 # Checkout
```

### Performance Considerations

1. **Eager Loading**: Siempre cargar relaciones necesarias
   ```php
   $cart->load(['items.product', 'items.variant']);
   ```

2. **Query Optimization**: Usar select específicos
   ```php
   Product::select('id', 'name', 'price', 'stock', 'is_active')->find($id);
   ```

3. **Caching**: Cachear configuración y tax_rate
   ```php
   Cache::remember('cart.tax_rate', 3600, fn() => config('cart.tax_rate'));
   ```

4. **Database Indexes**: Asegurar índices en columnas frecuentemente consultadas

5. **Transaction Scope**: Mantener transacciones lo más cortas posible

### Security Considerations

1. **Authorization**: Siempre validar ownership del carrito
2. **Price Validation**: Nunca confiar en precios del cliente
3. **Input Sanitization**: Laravel lo hace automáticamente
4. **Rate Limiting**: Aplicar en endpoints del carrito
5. **CSRF Protection**: Habilitado por defecto en Laravel
6. **SQL Injection**: Usar Eloquent y query builder (protección automática)

### Monitoring and Observability

1. **Logs**: Registrar eventos críticos (checkout, errores de stock, manipulación de precios)
2. **Metrics**: Trackear tasa de conversión de carrito a orden
3. **Alerts**: Notificar cuando stock crítico o errores frecuentes
4. **APM**: Monitorear performance de operaciones del carrito
