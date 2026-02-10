### ✅ TASKS

**Sprint/Phase 1: Migraciones Iniciales de Estructura**
- [x] **TASK-001**: Crear migración `create_users_table`.
  - Expected Outcome: Archivo de migración para la tabla `users` con `id`, `uuid`, campos de autenticación, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-004
  - Effort: 1 hora
  - Dependencies: Ninguna
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-002**: Crear migración `create_roles_table`.
  - Expected Outcome: Archivo de migración para la tabla `roles` con `id`, `uuid`, `name`, `display_name`, `description`, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-004
  - Effort: 1 hora
  - Dependencies: Ninguna
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-003**: Crear migración `create_role_user_table`.
  - Expected Outcome: Archivo de migración para la tabla `role_user` con `id`, `user_id`, `role_id`, `timestamps` y un índice único compuesto.
  - Requirements Traced: REQ-F-003, REQ-F-004
  - Effort: 1 hora
  - Dependencies: TASK-001, TASK-002
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-004**: Crear migración `create_categories_table`.
  - Expected Outcome: Archivo de migración para la tabla `categories` con `id`, `uuid`, `parent_id`, `name`, `slug`, `description`, `image`, `order`, `is_active`, `timestamps`, `softDeletes` y campos de auditoría (`created_by`, `updated_by`, `deleted_by`).
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004
  - Effort: 1.5 horas
  - Dependencies: TASK-001 (para `created_by`, `updated_by`, `deleted_by`)
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-005**: Crear migración `create_products_table`.
  - Expected Outcome: Archivo de migración para la tabla `products` con `id`, `uuid`, `category_id`, `name`, `slug`, `sku`, descripciones, precios, stock, umbrales, peso, dimensiones, flags, metadatos SEO, `timestamps`, `softDeletes` y campos de auditoría.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005, REQ-F-006
  - Effort: 2 horas
  - Dependencies: TASK-001, TASK-004
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-006**: Crear migración `create_product_images_table`.
  - Expected Outcome: Archivo de migración para la tabla `product_images` con `id`, `uuid`, `product_id`, `image_path`, `thumbnail_path`, `alt_text`, `is_primary`, `order`, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004
  - Effort: 1 hora
  - Dependencies: TASK-005
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-007**: Crear migración `create_product_variants_table`.
  - Expected Outcome: Archivo de migración para la tabla `product_variants` con `id`, `uuid`, `product_id`, `name`, `sku`, `price`, `stock`, `attributes` (JSON), `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-006
  - Effort: 1 hora
  - Dependencies: TASK-005
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-008**: Crear migración `create_carts_table`.
  - Expected Outcome: Archivo de migración para la tabla `carts` con `id`, `uuid`, `user_id`, `session_id`, `coupon_code`, `discount_amount`, `expires_at` y `timestamps`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005
  - Effort: 1 hora
  - Dependencies: TASK-001
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-009**: Crear migración `create_cart_items_table`.
  - Expected Outcome: Archivo de migración para la tabla `cart_items` con `id`, `uuid`, `cart_id`, `product_id`, `product_variant_id`, `quantity`, `price` y `timestamps`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005
  - Effort: 1 hora
  - Dependencies: TASK-008, TASK-005, TASK-007
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-010**: Crear migración `create_addresses_table`.
  - Expected Outcome: Archivo de migración para la tabla `addresses` con `id`, `uuid`, `user_id`, `type`, campos de dirección, `is_default`, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: TASK-001
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-011**: Crear migración `create_orders_table`.
  - Expected Outcome: Archivo de migración para la tabla `orders` con `id`, `uuid`, `user_id`, `order_number`, `status`, `payment_status`, campos de totales, métodos, direcciones, notas, fechas de estado, `timestamps`, `softDeletes` y campos de auditoría.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005, REQ-F-007
  - Effort: 2 horas
  - Dependencies: TASK-001, TASK-010
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-012**: Crear migración `create_order_items_table`.
  - Expected Outcome: Archivo de migración para la tabla `order_items` con `id`, `uuid`, `order_id`, `product_id`, `product_variant_id`, `product_name`, `product_sku`, `quantity`, `price`, `subtotal`, `tax`, `total`, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005
  - Effort: 1.5 horas
  - Dependencies: TASK-011, TASK-005, TASK-007
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-013**: Crear migración `create_payments_table`.
  - Expected Outcome: Archivo de migración para la tabla `payments` con `id`, `uuid`, `order_id`, `payment_method`, `transaction_id`, `amount`, `currency`, `status`, `gateway_response` (JSON), fechas de pago/reembolso, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-005, REQ-F-006, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: TASK-011
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-014**: Crear migración `create_coupons_table`.
  - Expected Outcome: Archivo de migración para la tabla `coupons` con `id`, `uuid`, `code`, `type`, `value`, límites de uso/compra, fechas de inicio/fin, `is_active`, `description`, `timestamps`, `softDeletes` y campos de auditoría.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-004, REQ-F-005, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: TASK-001
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-015**: Crear migración `create_coupon_user_table`.
  - Expected Outcome: Archivo de migración para la tabla `coupon_user` con `id`, `coupon_id`, `user_id`, `order_id`, `used_at` y `timestamps`.
  - Requirements Traced: REQ-F-003, REQ-F-004
  - Effort: 1 hora
  - Dependencies: TASK-014, TASK-001, TASK-011
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-016**: Crear migración `create_reviews_table`.
  - Expected Outcome: Archivo de migración para la tabla `reviews` con `id`, `uuid`, `product_id`, `user_id`, `order_id`, `rating`, `title`, `comment`, `is_verified_purchase`, `is_approved`, `approved_at`, `approved_by`, `helpful_count`, `timestamps` y `softDeletes`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004
  - Effort: 1.5 horas
  - Dependencies: TASK-005, TASK-001, TASK-011
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-017**: Crear migración `create_wishlist_table`.
  - Expected Outcome: Archivo de migración para la tabla `wishlist` con `id`, `user_id`, `product_id`, `timestamps` y un índice único compuesto.
  - Requirements Traced: REQ-F-003, REQ-F-004
  - Effort: 1 hora
  - Dependencies: TASK-001, TASK-005
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-018**: Crear migración `create_product_views_table`.
  - Expected Outcome: Archivo de migración para la tabla `product_views` con `id`, `product_id`, `user_id`, `session_id`, `ip_address`, `user_agent` y `viewed_at`.
  - Requirements Traced: REQ-F-003, REQ-F-004
  - Effort: 1 hora
  - Dependencies: TASK-005, TASK-001
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-019**: Crear migración `create_notifications_table`.
  - Expected Outcome: Archivo de migración para la tabla `notifications` con `id`, `uuid`, `type`, `notifiable_type`, `notifiable_id`, `data` (JSON), `read_at` y `timestamps`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-004, REQ-F-006
  - Effort: 1 hora
  - Dependencies: Ninguna
  - DoD: Migración creada y validada sintácticamente.

- [x] **TASK-020**: Crear migración `create_activity_log_table`.
  - Expected Outcome: Archivo de migración para la tabla `activity_log` con `id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties` (JSON), y `timestamps`.
  - Requirements Traced: REQ-F-002, REQ-F-004, REQ-F-006
  - Effort: 1.5 horas
  - Dependencies: Ninguna
  - DoD: Migración creada y validada sintácticamente.

**Sprint/Phase 2: Modelos Eloquent**
- [x] **TASK-021**: Crear/Modificar modelo `User`.
  - Expected Outcome: Modelo `User` con `HasUuids`, `HasAuditFields`, `$fillable`, `$casts`, relaciones (`roles`, `addresses`, `carts`, `orders`, `reviews`, `wishlist`), y scope `active()`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004
  - Effort: 2 horas
  - Dependencies: `HasAuditFields` trait, `Role` model, `Address` model, `Cart` model, `Order` model, `Review` model, `Wishlist` model.
  - DoD: Modelo `User` creado/modificado y validado sintácticamente.

- [x] **TASK-022**: Crear modelo `Role`.
  - Expected Outcome: Modelo `Role` con `HasUuids`, `HasAuditFields`, `$fillable`, y relación `users`.
  - Requirements Traced: REQ-F-001, REQ-F-003
  - Effort: 1 hora
  - Dependencies: `HasAuditFields` trait, `User` model.
  - DoD: Modelo `Role` creado y validado sintácticamente.

- [x] **TASK-023**: Crear modelo `Category`.
  - Expected Outcome: Modelo `Category` con `HasUuids`, `HasAuditFields`, `$fillable`, `$casts`, relaciones (`parent`, `children`, `products`), y scopes `active()`, `root()`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004
  - Effort: 1.5 horas
  - Dependencies: `HasAuditFields` trait, `Product` model.
  - DoD: Modelo `Category` creado y validado sintácticamente.

- [x] **TASK-024**: Crear/Modificar modelo `Product`.
  - Expected Outcome: Modelo `Product` con `HasUuids`, `HasAuditFields`, `$fillable`, `$casts`, relaciones (`category`, `images`, `variants`, `reviews`), y scopes `active()`, `featured()`, `inStock()`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-004, REQ-F-006
  - Effort: 2 horas
  - Dependencies: `HasAuditFields` trait, `Category` model, `ProductImage` model, `ProductVariant` model, `Review` model.
  - DoD: Modelo `Product` creado/modificado y validado sintácticamente.

- [x] **TASK-025**: Crear modelo `ProductImage`.
  - Expected Outcome: Modelo `ProductImage` con `HasUuids`, `$casts`, y relación `product`.
  - Requirements Traced: REQ-F-001, REQ-F-003
  - Effort: 1 hora
  - Dependencies: `Product` model.
  - DoD: Modelo `ProductImage` creado y validado sintácticamente.

- [x] **TASK-026**: Crear modelo `ProductVariant`.
  - Expected Outcome: Modelo `ProductVariant` con `HasUuids`, `$casts`, y relación `product`.
  - Requirements Traced: REQ-F-001, REQ-F-003, REQ-F-006
  - Effort: 1 hora
  - Dependencies: `Product` model.
  - DoD: Modelo `ProductVariant` creado y validado sintácticamente.

- [x] **TASK-027**: Crear modelo `Cart`.
  - Expected Outcome: Modelo `Cart` con `HasUuids`, `$casts`, relaciones (`user`, `items`).
  - Requirements Traced: REQ-F-001, REQ-F-003
  - Effort: 1 hora
  - Dependencies: `User` model, `CartItem` model.
  - DoD: Modelo `Cart` creado y validado sintácticamente.

- [x] **TASK-028**: Crear modelo `CartItem`.
  - Expected Outcome: Modelo `CartItem` con `HasUuids`, `$casts`, relaciones (`cart`, `product`, `variant`).
  - Requirements Traced: REQ-F-001, REQ-F-003
  - Effort: 1 hora
  - Dependencies: `Cart` model, `Product` model, `ProductVariant` model.
  - DoD: Modelo `CartItem` creado y validado sintácticamente.

- [x] **TASK-029**: Crear modelo `Address`.
  - Expected Outcome: Modelo `Address` con `HasUuids`, `$casts`, relación `user`, y Enum `AddressType`.
  - Requirements Traced: REQ-F-001, REQ-F-003, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: `User` model, `AddressType` enum.
  - DoD: Modelo `Address` creado y validado sintácticamente.

- [x] **TASK-030**: Crear/Modificar modelo `Order`.
  - Expected Outcome: Modelo `Order` con `HasUuids`, `HasAuditFields`, `$casts`, relaciones (`user`, `shippingAddress`, `billingAddress`, `items`, `payments`), y Enums `OrderStatus`, `PaymentStatus`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-005, REQ-F-007
  - Effort: 2 horas
  - Dependencies: `HasAuditFields` trait, `User` model, `Address` model, `OrderItem` model, `Payment` model, `OrderStatus` enum, `PaymentStatus` enum.
  - DoD: Modelo `Order` creado/modificado y validado sintácticamente.

- [x] **TASK-031**: Crear/Modificar modelo `OrderItem`.
  - Expected Outcome: Modelo `OrderItem` con `HasUuids`, `$casts`, relaciones (`order`, `product`, `variant`).
  - Requirements Traced: REQ-F-001, REQ-F-003, REQ-F-005
  - Effort: 1.5 horas
  - Dependencies: `Order` model, `Product` model, `ProductVariant` model.
  - DoD: Modelo `OrderItem` creado/modificado y validado sintácticamente.

- [x] **TASK-032**: Crear modelo `Payment`.
  - Expected Outcome: Modelo `Payment` con `HasUuids`, `$casts`, relación `order`, y Enum `PaymentStatus`.
  - Requirements Traced: REQ-F-001, REQ-F-003, REQ-F-005, REQ-F-006, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: `Order` model, `PaymentStatus` enum.
  - DoD: Modelo `Payment` creado y validado sintácticamente.

- [x] **TASK-033**: Crear modelo `Coupon`.
  - Expected Outcome: Modelo `Coupon` con `HasUuids`, `HasAuditFields`, `$casts`, relación `users` (many-to-many), y Enum `CouponType`.
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003, REQ-F-005, REQ-F-007
  - Effort: 1.5 horas
  - Dependencies: `HasAuditFields` trait, `User` model, `CouponType` enum.
  - DoD: Modelo `Coupon` creado y validado sintácticamente.

- [x] **TASK-034**: Crear modelo `CouponUser`.
  - Expected Outcome: Modelo `CouponUser` con `$casts`, y relaciones (`coupon`, `user`, `order`).
  - Requirements Traced: REQ-F-003
  - Effort: 1 hora
  - Dependencies: `Coupon` model, `User` model, `Order` model.
  - DoD: Modelo `CouponUser` creado y validado sintácticamente.

- [x] **TASK-035**: Crear modelo `Review`.
  - Expected Outcome: Modelo `Review` con `HasUuids`, `HasAuditFields`, `$casts`, relaciones (`product`, `user`, `order`, `approvedBy`).
  - Requirements Traced: REQ-F-001, REQ-F-002, REQ-F-003
  - Effort: 1.5 horas
  - Dependencies: `HasAuditFields` trait, `Product` model, `User` model, `Order` model.
  - DoD: Modelo `Review` creado y validado sintácticamente.

- [x] **TASK-036**: Crear modelo `Wishlist`.
  - Expected Outcome: Modelo `Wishlist` con relaciones (`user`, `product`).
  - Requirements Traced: REQ-F-003
  - Effort: 1 hora
  - Dependencies: `User` model, `Product` model.
  - DoD: Modelo `Wishlist` creado y validado sintácticamente.

- [x] **TASK-037**: Crear modelo `ProductView`.
  - Expected Outcome: Modelo `ProductView` con relaciones (`product`, `user`).
  - Requirements Traced: REQ-F-003
  - Effort: 1 hora
  - Dependencies: `Product` model, `User` model.
  - DoD: Modelo `ProductView` creado y validado sintácticamente.

- [x] **TASK-038**: Crear modelo `Notification`.
  - Expected Outcome: Modelo `Notification` con `HasUuids`, `$casts`, y relación `notifiable`.
  - Requirements Traced: REQ-F-001, REQ-F-006
  - Effort: 1 hora
  - Dependencies: Ninguna.
  - DoD: Modelo `Notification` creado y validado sintácticamente.

- [x] **TASK-039**: Crear modelo `ActivityLog`.
  - Expected Outcome: Modelo `ActivityLog` con `$casts`, y relaciones `subject`, `causer`.
  - Requirements Traced: REQ-F-006
  - Effort: 1.5 horas
  - Dependencies: Ninguna.
  - DoD: Modelo `ActivityLog` creado y validado sintácticamente.

**Validation & Testing:**
- Unit Testing: N/A (las migraciones son definiciones de esquema, no lógica de negocio).
- Integration Testing: Ejecutar `php artisan migrate` y `php artisan migrate:rollback` para verificar la creación y eliminación correcta de las tablas.
- Clinical Validation: N/A.
- Compliance Testing: Revisión manual de cada migración para asegurar el cumplimiento de todas las reglas obligatorias (IDs híbridos, auditoría, FKs, índices, soft deletes).

**Deployment Strategy:**
- Environment Setup: Las migraciones se ejecutarán en entornos de desarrollo, staging y producción.
- CI/CD Pipeline: Las migraciones deben ser parte del pipeline de CI/CD, ejecutándose automáticamente en los entornos de staging y producción después de un despliegue exitoso.
- Monitoring: Monitorear logs de la base de datos durante la ejecución de migraciones para detectar errores.