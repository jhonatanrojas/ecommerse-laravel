# Implementation Plan: Shopping Cart Logic

## Overview

Este plan de implementación desglosa el diseño del carrito de compra en tareas discretas de codificación. Cada tarea construye sobre las anteriores, comenzando con la infraestructura base (DTOs, excepciones, configuración), luego los servicios core, validadores, calculadora, y finalmente el proceso de checkout y eventos.

El enfoque es incremental: implementar funcionalidad core primero, validar con tests, y luego añadir características adicionales. Los tests de propiedades están marcados como opcionales (*) para permitir un MVP más rápido, pero son altamente recomendados para garantizar correctitud.

## Tasks

- [x] 1. Setup: Configuration and Infrastructure
  - [x] 1.1 Create cart configuration file
    - Create `config/cart.php` with guest_cart_expiration_days, max_item_quantity, tax_rate, default_shipping_cost
    - Add corresponding environment variables to `.env.example`
    - _Requirements: 12.1, 13.1, 4.3_

  - [x] 1.2 Create custom exceptions hierarchy
    - Create `app/Exceptions/Cart/CartException.php` and subclasses (CartExpiredException, CartNotFoundException, UnauthorizedCartAccessException)
    - Create `app/Exceptions/Cart/CartItemException.php` and subclasses (ProductNotFoundException, ProductInactiveException, InsufficientStockException, InvalidQuantityException)
    - Create `app/Exceptions/Cart/CouponException.php` and subclasses (CouponNotFoundException, CouponInactiveException, CouponExpiredException, CouponUsageLimitException, MinimumPurchaseNotMetException)
    - Create `app/Exceptions/Cart/CheckoutException.php` and subclass (CheckoutValidationException)
    - _Requirements: All error handling_

  - [x] 1.3 Create Data Transfer Objects (DTOs)
    - Create `app/Services/Cart/DTOs/CartSummary.php` with readonly properties
    - Create `app/Services/Cart/DTOs/CheckoutData.php` with readonly properties
    - Create `app/Services/Cart/DTOs/ValidationResult.php` with success/fail static methods
    - _Requirements: 2.6, 7.1_

- [x] 2. Core Services: Validators
  - [x] 2.1 Implement StockValidator service
    - Create `app/Services/Cart/StockValidator.php`
    - Implement `validateProduct()` to check existence and is_active
    - Implement `validateVariant()` to check existence and parent product is_active
    - Implement `validateStock()` to check quantity against available stock
    - Implement `getAvailableStock()` to return current stock for product or variant
    - Implement `isProductActive()` helper method
    - _Requirements: 3.1, 3.2, 3.3_

  - [x] 2.2 Write property tests for StockValidator
    - **Property 12: Active product validation**
    - **Property 13: Active variant validation**
    - **Property 14: Stock availability validation**
    - **Validates: Requirements 3.1, 3.2, 3.3**

  - [x] 2.3 Implement CouponValidator service
    - Create `app/Services/Cart/CouponValidator.php`
    - Implement `validate()` method that checks all coupon rules
    - Implement `isActive()` to check is_active field
    - Implement `isWithinDateRange()` to check starts_at and expires_at
    - Implement `hasReachedUsageLimit()` to check used_count vs usage_limit
    - Implement `hasUserReachedLimit()` to check user-specific usage via coupon_user pivot
    - Implement `meetsMinimumPurchase()` to check subtotal vs min_purchase_amount
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ]* 2.4 Write property test for CouponValidator
    - **Property 22: Comprehensive coupon validation**
    - **Validates: Requirements 5.1, 5.2, 5.3, 5.4, 5.5**

- [-] 3. Core Services: Calculator

  - [x] 3.1 Implement CartCalculator service
    - Create `app/Services/Cart/CartCalculator.php`
    - Implement `calculateSubtotal()` to sum (price × quantity) for all cart items
    - Implement `calculateDiscount()` for both fixed and percentage coupon types
    - Implement `calculateTax()` to apply tax_rate on (subtotal - discount)
    - Implement `calculateShipping()` to return configured shipping cost
    - Implement `calculateTotal()` as (subtotal - discount + tax + shipping)
    - Implement `getTaxRate()` helper to get rate from config
    - _Requirements: 4.1, 4.2, 4.3, 4.5, 5.6, 5.7, 5.8_

  - [ ] 3.2 Write property tests for CartCalculator
    - **Property 16: Subtotal calculation correctness**
    - **Property 17: Fixed coupon discount calculation**
    - **Property 18: Percentage coupon discount calculation**
    - **Property 19: Tax calculation correctness**
    - **Property 20: Total calculation correctness**
    - **Validates: Requirements 4.1, 4.2, 4.3, 4.5, 5.6, 5.7, 5.8**

- [x] 4. Core Services: CartRepository (Optional Pattern)
  - [x] 4.1 Implement CartRepository
    - Create `app/Services/Cart/CartRepository.php`
    - Implement `findByUser()` to find cart by user_id
    - Implement `findBySession()` to find cart by session_id
    - Implement `findByUuid()` to find cart by uuid
    - Implement `create()` to create new cart with data
    - Implement `update()` to update cart with data
    - Implement `deleteExpiredCarts()` to clean up expired carts
    - _Requirements: 1.1, 1.2, 12.4_

- [x] 5. Core Services: CartService - Cart Lifecycle
  - [x] 5.1 Implement cart lifecycle methods in CartService
    - Create `app/Services/Cart/CartService.php` with constructor injection
    - Implement `getOrCreateCart()` to find or create cart for user/session
    - Implement `findCart()` to find existing cart
    - Implement `isCartExpired()` to check expires_at
    - Set expires_at to 30 days for guest carts, null for authenticated carts
    - _Requirements: 1.1, 1.2, 2.1, 12.1, 12.2_

  - [ ]* 5.2 Write property tests for cart lifecycle
    - **Property 1: Authenticated cart association**
    - **Property 2: Guest cart association**
    - **Property 6: Automatic cart creation**
    - **Property 36: Guest cart expiration setting**
    - **Property 37: Authenticated cart no expiration**
    - **Validates: Requirements 1.1, 1.2, 2.1, 12.1, 12.2**

  - [x] 5.3 Implement cart expiration validation
    - Add expiration check at the beginning of all cart operations
    - Throw CartExpiredException if cart is expired
    - _Requirements: 1.4_

  - [ ]* 5.4 Write property test for cart expiration
    - **Property 4: Cart expiration enforcement**
    - **Validates: Requirements 1.4**

- [ ] 6. Core Services: CartService - Cart Items CRUD
  - [x] 6.1 Implement addItem method
    - Implement `addItem()` with product_id, variant_id, quantity parameters
    - Validate product/variant using StockValidator
    - Validate stock availability
    - Validate quantity limits (min 1, max from config)
    - Get current price from database (never trust client)
    - Check if item already exists in cart, if so update quantity instead
    - Create CartItem with validated data within transaction
    - Use lockForUpdate on product/variant for stock validation
    - _Requirements: 2.2, 3.1, 3.2, 3.3, 3.4, 13.1, 13.2_

  - [ ]* 6.2 Write property tests for addItem
    - **Property 7: Cart item structure completeness**
    - **Property 15: Price integrity enforcement**
    - **Property 38: Maximum quantity validation**
    - **Property 39: Minimum quantity validation**
    - **Validates: Requirements 2.2, 3.4, 13.1, 13.2**

  - [x] 6.3 Implement updateItemQuantity method
    - Implement `updateItemQuantity()` with CartItem and new quantity
    - Validate new quantity against stock
    - Validate quantity limits
    - Update CartItem quantity within transaction
    - Use lockForUpdate on product/variant for stock validation
    - _Requirements: 2.3, 3.3, 13.1, 13.2_

  - [ ]* 6.4 Write property test for updateItemQuantity
    - **Property 8: Quantity update reflection**
    - **Validates: Requirements 2.3**

  - [x] 6.5 Implement removeItem and clearCart methods
    - Implement `removeItem()` to delete a CartItem
    - Implement `clearCart()` to delete all CartItems for a cart
    - _Requirements: 2.4, 2.5_

  - [ ]* 6.6 Write property tests for item removal
    - **Property 9: Item removal completeness**
    - **Property 10: Cart clearing completeness**
    - **Validates: Requirements 2.4, 2.5**

- [x] 7. Core Services: CartService - Cart Summary and Totals
  - [x] 7.1 Implement getCartSummary method
    - Implement `getCartSummary()` to return CartSummary DTO
    - Use CartCalculator to compute all totals
    - Eager load cart items with products and variants
    - Return CartSummary with subtotal, discount, tax, shipping_cost, total, item_count, coupon_code
    - _Requirements: 2.6, 4.1, 4.2, 4.3, 4.5_

  - [ ]* 7.2 Write property tests for cart summary
    - **Property 11: Cart summary structure**
    - **Property 21: Totals recalculation on change**
    - **Validates: Requirements 2.6, 4.6**

  - [x] 7.3 Implement recalculateTotals method
    - Implement `recalculateTotals()` to refresh all calculations
    - Call after any cart modification
    - _Requirements: 4.6_

- [x] 8. Core Services: CartService - Coupon Management
  - [x] 8.1 Implement applyCoupon method
    - Implement `applyCoupon()` with cart, coupon_code, and user parameters
    - Find coupon by code or throw CouponNotFoundException
    - Validate coupon using CouponValidator
    - Calculate discount using CartCalculator
    - Update cart with coupon_code and discount_amount within transaction
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8, 5.9_

  - [ ]* 8.2 Write property tests for applyCoupon
    - **Property 22: Comprehensive coupon validation**
    - **Property 23: Coupon persistence after application**
    - **Validates: Requirements 5.1-5.9**

  - [x] 8.3 Implement removeCoupon method
    - Implement `removeCoupon()` to clear coupon_code and discount_amount
    - Recalculate totals after removal
    - _Requirements: 5.10_

  - [ ]* 8.4 Write property test for removeCoupon
    - **Property 24: Coupon removal completeness**
    - **Validates: Requirements 5.10**

- [x] 9. Core Services: CartService - Cart Migration
  - [x] 9.1 Implement migrateGuestCartToUser method
    - Implement `migrateGuestCartToUser()` with session_id and user parameters
    - Find guest cart by session_id
    - Find or create user cart
    - For each guest cart item, check if same product/variant exists in user cart
    - If exists, sum quantities; if not, move item to user cart
    - Delete guest cart after migration
    - Execute within transaction
    - _Requirements: 1.3, 1.5_

  - [ ]* 9.2 Write property tests for cart migration
    - **Property 3: Cart migration preserves items**
    - **Property 5: Item consolidation during migration**
    - **Validates: Requirements 1.3, 1.5**

- [ ] 10. Checkpoint - Core Services Complete
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 11. Checkout: CheckoutService Implementation
  - [ ] 11.1 Create CheckoutService
    - Create `app/Services/Cart/CheckoutService.php`
    - Inject OrderService (or create inline), StockManager, CouponManager dependencies
    - Implement `processCheckout()` as main orchestration method
    - _Requirements: 7.1_

  - [x] 11.2 Implement order creation in checkout
    - Implement `createOrder()` private method
    - Generate unique order_number
    - Create Order with user_id, status, payment_status, and all totals from cart
    - Copy coupon_code if present
    - Copy shipping and billing address IDs from CheckoutData
    - _Requirements: 7.1_

  - [ ]* 11.3 Write property test for order creation
    - **Property 27: Order creation completeness**
    - **Validates: Requirements 7.1**

  - [x] 11.4 Implement order items creation in checkout
    - Implement `createOrderItems()` private method
    - For each CartItem, create OrderItem with snapshot of product data
    - Copy product_id, product_variant_id, product_name, product_sku, quantity, price
    - Calculate subtotal, tax, and total for each item
    - _Requirements: 7.2_

  - [ ]* 11.5 Write property test for order items snapshot
    - **Property 28: Order items snapshot accuracy**
    - **Property 33: Order item price immutability**
    - **Validates: Requirements 7.2, 14.2**

  - [x] 11.6 Implement stock decrement in checkout
    - Implement `decrementStock()` private method
    - For each CartItem, decrement stock of Product or ProductVariant
    - Use lockForUpdate to prevent race conditions
    - Validate stock is still sufficient before decrement
    - _Requirements: 7.3_

  - [x] 11.7 Write property test for stock decrement
    - **Property 29: Stock decrement on checkout**
    - **Validates: Requirements 7.3**

  - [x] 11.8 Implement coupon usage increment in checkout
    - Implement `incrementCouponUsage()` private method
    - If cart has coupon, increment Coupon.used_count
    - Create entry in coupon_user pivot table for user tracking
    - _Requirements: 7.4_

  - [ ]* 11.9 Write property test for coupon usage
    - **Property 30: Coupon usage increment on checkout**
    - **Validates: Requirements 7.4**

  - [x] 11.10 Implement cart clearing in checkout
    - Implement `clearCart()` private method to delete all CartItems
    - _Requirements: 7.5_

  - [ ]* 11.11 Write property test for cart clearing
    - **Property 31: Cart clearing after checkout**
    - **Validates: Requirements 7.5**

  - [x] 11.12 Wrap entire checkout in database transaction
    - Wrap all checkout steps in DB::transaction()
    - If any step fails, rollback entire transaction
    - Throw CheckoutException with descriptive message on failure
    - _Requirements: 7.6, 7.7_

  - [ ]* 11.13 Write property test for checkout atomicity
    - **Property 32: Checkout atomicity**
    - **Validates: Requirements 7.6**

- [x] 12. Checkout: Integrate CheckoutService into CartService
  - [x] 12.1 Add checkout method to CartService
    - Implement `checkout()` method in CartService
    - Delegate to CheckoutService.processCheckout()
    - Validate cart is not empty before checkout
    - Validate cart is not expired
    - _Requirements: 7.1_

- [ ] 13. Authorization: Cart Ownership Validation
  - [x] 13.1 Implement authorizeCartAccess method
    - Implement `authorizeCartAccess()` in CartService
    - For authenticated users, verify cart.user_id matches current user
    - For guests, verify cart.session_id matches current session
    - Throw UnauthorizedCartAccessException if mismatch
    - _Requirements: 9.1, 9.2_

  - [x] 13.2 Add authorization checks to all cart operations
    - Call authorizeCartAccess() at the beginning of addItem, updateItemQuantity, removeItem, clearCart, applyCoupon, removeCoupon, checkout
    - _Requirements: 9.1, 9.2_

  - [ ]* 13.3 Write property test for authorization
    - **Property 35: Cart ownership validation**
    - **Validates: Requirements 9.1, 9.2**

- [x] 14. Checkpoint - Checkout and Authorization Complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 15. Events: Create Event Classes
  - [x] 15.1 Create cart lifecycle events
    - Create `app/Events/Cart/CartCreated.php` with Cart property
    - Create `app/Events/Cart/CartItemAdded.php` with Cart and CartItem properties
    - Create `app/Events/Cart/CartItemUpdated.php` with Cart, CartItem, and oldQuantity properties
    - Create `app/Events/Cart/CartItemRemoved.php` with Cart and CartItem properties
    - Create `app/Events/Cart/CartCleared.php` with Cart property
    - Create `app/Events/Cart/CartMigrated.php` with guestCart, userCart, and User properties
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [x] 15.2 Create coupon events
    - Create `app/Events/Cart/CouponApplied.php` with Cart, Coupon, and discount properties
    - Create `app/Events/Cart/CouponRemoved.php` with Cart and couponCode properties
    - _Requirements: 8.6, 8.7_

  - [x] 15.3 Create checkout events
    - Create `app/Events/Cart/CartCheckedOut.php` with Cart and Order properties
    - Create `app/Events/Cart/CheckoutFailed.php` with Cart and reason properties
    - _Requirements: 8.8_

- [x] 16. Events: Emit Events from CartService
  - [x] 16.1 Emit events in cart lifecycle methods
    - Emit CartCreated in getOrCreateCart() when creating new cart
    - Emit CartItemAdded in addItem() after successful addition
    - Emit CartItemUpdated in updateItemQuantity() after successful update
    - Emit CartItemRemoved in removeItem() after successful removal
    - Emit CartCleared in clearCart() after clearing
    - Emit CartMigrated in migrateGuestCartToUser() after migration
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [x] 16.2 Emit events in coupon methods
    - Emit CouponApplied in applyCoupon() after successful application
    - Emit CouponRemoved in removeCoupon() after removal
    - _Requirements: 8.6, 8.7_

  - [x] 16.3 Emit events in checkout
    - Emit CartCheckedOut in checkout() after successful checkout
    - Emit CheckoutFailed in checkout() catch block on failure
    - _Requirements: 8.8_

  - [x]* 16.4 Write property test for event emission
    - **Property 34: Cart lifecycle events emission**
    - **Validates: Requirements 8.1-8.8**

- [x] 17. Events: Create Event Listeners (Optional Side Effects)
  - [x] 17.1 Create example listeners
    - Create `app/Listeners/Cart/SendCartAbandonmentEmail.php` (optional)
    - Create `app/Listeners/Cart/UpdateAnalytics.php` (optional)
    - Create `app/Listeners/Cart/SendOrderConfirmationEmail.php` for CartCheckedOut
    - Register listeners in EventServiceProvider
    - _Requirements: 8.8_

- [ ] 18. Artisan Commands
  - [ ] 18.1 Create cart:clean-expired command
    - Create `app/Console/Commands/CleanExpiredCarts.php`
    - Implement handle() to delete carts where expires_at < now()
    - Log number of carts deleted
    - _Requirements: 12.4_

  - [ ] 18.2 Create cart:migrate-guest command (optional)
    - Create `app/Console/Commands/MigrateGuestCart.php`
    - Accept session_id and user_id arguments
    - Call CartService.migrateGuestCartToUser()
    - _Requirements: 1.3_

- [x] 19. API: Controllers and Routes
  - [x] 19.1 Create CartController
    - Create `app/Http/Controllers/Api/CartController.php`
    - Inject CartService in constructor
    - Implement index() to get cart summary
    - Implement store() to add item (validate with Form Request)
    - Implement update() to update item quantity
    - Implement destroy() to remove item
    - Implement clear() to empty cart
    - Use API Resources for responses
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 19.2 Create CouponController
    - Create `app/Http/Controllers/Api/CouponController.php`
    - Inject CartService in constructor
    - Implement store() to apply coupon
    - Implement destroy() to remove coupon
    - _Requirements: 5.9, 5.10_

  - [x] 19.3 Create CheckoutController
    - Create `app/Http/Controllers/Api/CheckoutController.php`
    - Inject CartService in constructor
    - Implement store() to process checkout (validate with Form Request)
    - Return Order resource on success
    - _Requirements: 7.1_

  - [x] 19.4 Create Form Requests for validation
    - Create `app/Http/Requests/Cart/AddItemRequest.php` to validate product_id, variant_id, quantity
    - Create `app/Http/Requests/Cart/UpdateItemRequest.php` to validate quantity
    - Create `app/Http/Requests/Cart/ApplyCouponRequest.php` to validate coupon_code
    - Create `app/Http/Requests/Cart/CheckoutRequest.php` to validate checkout data
    - _Requirements: All validation_

  - [x] 19.5 Create API Resources for responses
    - Create `app/Http/Resources/CartResource.php` to format cart with items
    - Create `app/Http/Resources/CartItemResource.php` to format cart item
    - Create `app/Http/Resources/CartSummaryResource.php` to format summary
    - _Requirements: 2.6_

  - [x] 19.6 Register API routes
    - Add routes in `routes/api.php` for cart operations
    - Apply auth:sanctum middleware for authenticated routes
    - Apply guest middleware for guest cart routes
    - Group routes under /api/cart prefix
    - _Requirements: All_


- [ ] 20. Service Provider Registration
  - [ ] 20.1 Register services in AppServiceProvider
    - Register CartService, CartCalculator, CouponValidator, StockValidator as singletons
    - _Requirements: All_

- [ ] 21. Integration Tests
  - [ ] 21.1 Write integration test for complete cart flow
    - Test: Create cart → Add items → Apply coupon → Checkout → Verify order
    - Test: Guest cart → Login → Verify migration → Checkout
    - Test: Concurrent additions → Verify no overselling
    - _Requirements: All_

- [ ] 22. Concurrency Tests
  - [ ]* 22.1 Write property test for concurrency
    - **Property 25: No overselling under concurrency**
    - **Property 26: Transaction rollback on validation failure**
    - **Validates: Requirements 6.2, 6.4**

- [ ] 23. Final Checkpoint - Complete System
  - Ensure all tests pass, ask the user if questions arise.
  - Run full test suite including property tests
  - Verify all requirements are covered
  - Check code coverage (target 80%+)

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP delivery
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties across many inputs
- Unit tests validate specific examples and edge cases
- All cart operations use database transactions for atomicity
- Authorization checks are mandatory on all cart operations
- Price validation from database is critical for security
- Stock validation with locking prevents overselling
- Events enable decoupled side effects like emails and analytics
