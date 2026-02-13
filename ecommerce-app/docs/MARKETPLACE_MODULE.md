# Módulo Marketplace B2B (Laravel 10)

## Incluye
- Guard `vendor`, middleware `vendor.approved`, login/registro/logout de vendedores.
- Modelo de dominio marketplace: `Vendor`, `VendorProduct`, `VendorOrder`, `VendorPayout`, `VendorShippingMethod`, `VendorDispute`.
- Split automático de órdenes por vendedor al checkout (`VendorOrderService`).
- Comisiones por prioridad: categoría > vendedor > global (`CommissionService`).
- Liquidaciones manuales/automáticas con base para Stripe Connect / PayPal Payouts (`VendorPayoutService`).
- Panel web vendor completo: productos, órdenes, envíos, payouts, perfil, disputas.
- Panel web admin de marketplace: vendedores, moderación de productos, payouts y disputas.
- API marketplace: registro vendor, perfil público, productos por vendor, catálogo marketplace con filtros.

## Configuración relevante
Tabla `store_settings`:
- `marketplace_commission_rate`
- `auto_approve_vendors`
- `auto_approve_vendor_products`
- `enable_automatic_payouts`

Tabla `categories`:
- `commission_rate` (opcional)

## Comando de payouts automáticos
```bash
php artisan marketplace:payouts:auto
```
Programado en `app/Console/Kernel.php` para ejecución diaria a las 02:00.

## Flujo de checkout marketplace
1. Checkout crea `order` y `order_items`.
2. Cada `order_item` se asocia al `vendor_id` del `vendor_product` aprobado/activo.
3. `VendorOrderService` agrupa por vendedor y crea/actualiza `vendor_orders`.
4. Se calcula comisión y ganancia por línea usando `CommissionService`.

## Moderación
- Producto creado por vendor entra en pending (o auto-aprobado según setting).
- Admin aprueba/rechaza en `admin/vendor-products`.
- Historial básico en `vendor_products.moderation_history`.
