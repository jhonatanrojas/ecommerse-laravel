# Instalación del Módulo de Estatus de Envíos

## Resumen Ejecutivo

Se ha creado un módulo completo para gestionar dinámicamente los estatus de envío de órdenes desde el panel administrativo. Este módulo reemplaza el sistema anterior basado en enums por un sistema flexible y administrable.

## Archivos Creados

### Backend

#### Modelos
- ✅ `app/Models/ShippingStatus.php` - Modelo principal con relaciones y scopes

#### Controladores
- ✅ `app/Http/Controllers/Admin/AdminShippingStatusController.php` - CRUD de estatus
- ✅ `app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php` - Actualización desde orden

#### Requests (Validación)
- ✅ `app/Http/Requests/Admin/StoreShippingStatusRequest.php` - Validación para crear
- ✅ `app/Http/Requests/Admin/UpdateShippingStatusRequest.php` - Validación para actualizar
- ✅ `app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php` - Validación para actualizar desde orden

#### Resources (API)
- ✅ `app/Http/Resources/ShippingStatusResource.php` - Formato de respuesta API

#### Base de Datos
- ✅ `database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php`
- ✅ `database/seeders/ShippingStatusSeeder.php`
- ✅ `database/factories/ShippingStatusFactory.php`

### Frontend

#### Vistas
- ✅ `resources/views/admin/shipping-statuses/index.blade.php` - Panel de gestión

### Archivos Modificados

- ✅ `app/Models/Order.php` - Agregada relación `shippingStatus()` y método `setShippingStatus()`
- ✅ `app/Http/Resources/OrderResource.php` - Agregado campo `shipping_status`
- ✅ `app/Repositories/Eloquent/EloquentOrderRepository.php` - Agregado `shippingStatus` en eager loading
- ✅ `routes/admin.php` - Agregadas rutas del módulo
- ✅ `resources/views/admin/orders/show.blade.php` - Agregada sección de estatus de envío

### Documentación
- ✅ `SHIPPING_STATUS_MODULE.md` - Documentación completa del módulo
- ✅ `SHIPPING_STATUS_INSTALLATION.md` - Este archivo

## Pasos de Instalación

### 1. Ejecutar la Migración

```bash
php artisan migrate
```

Esta migración automáticamente:
- Crea la tabla `shipping_statuses`
- Inserta 7 estatus iniciales predefinidos
- Agrega la columna `shipping_status_id` a la tabla `orders`
- Asigna el estatus por defecto a todas las órdenes existentes

### 2. Verificar la Instalación

```bash
# Verificar que la tabla fue creada
php artisan tinker
>>> \App\Models\ShippingStatus::count()
# Debería retornar: 7

# Verificar que las órdenes tienen estatus asignado
>>> \App\Models\Order::whereNotNull('shipping_status_id')->count()
```

### 3. (Opcional) Ejecutar el Seeder Manualmente

Si necesitas recrear los estatus iniciales:

```bash
php artisan db:seed --class=ShippingStatusSeeder
```

### 4. Limpiar Caché

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Rutas Disponibles

### Panel de Gestión de Estatus
```
GET    /admin/shipping-statuses           - Listar estatus
POST   /admin/shipping-statuses           - Crear estatus
PUT    /admin/shipping-statuses/{id}      - Actualizar estatus
PATCH  /admin/shipping-statuses/{id}/toggle   - Activar/Desactivar
PATCH  /admin/shipping-statuses/{id}/default  - Marcar como default
DELETE /admin/shipping-statuses/{id}      - Eliminar estatus
```

### Actualización desde Orden
```
PATCH  /admin/orders/{uuid}/shipping-status   - Actualizar estatus de envío
```

## Estatus Iniciales Creados

1. **Pendiente de Envío** (pending_shipment) - #F59E0B - Default
2. **Preparando** (preparing) - #3B82F6
3. **Enviado** (shipped) - #8B5CF6
4. **En Tránsito** (in_transit) - #06B6D4
5. **Entregado** (delivered) - #10B981
6. **Devuelto** (returned) - #F97316
7. **Cancelado** (cancelled) - #EF4444

## Uso Básico

### Acceder al Panel de Gestión

1. Inicia sesión en el panel admin
2. Navega a `/admin/shipping-statuses`
3. Desde ahí puedes:
   - Ver todos los estatus configurados
   - Crear nuevos estatus
   - Editar estatus existentes
   - Activar/desactivar estatus
   - Marcar un estatus como predeterminado
   - Eliminar estatus (si no están en uso)

### Actualizar Estatus desde una Orden

1. Ve a `/admin/orders/{uuid}`
2. En la columna lateral, encontrarás la sección "Estatus de Envío"
3. Selecciona el nuevo estatus del dropdown
4. Haz clic en "Actualizar estatus de envío"

El sistema automáticamente:
- Actualiza el `shipping_status_id` de la orden
- Registra la auditoría en logs
- Actualiza `shipped_at` si el slug es "shipped"
- Actualiza `delivered_at` si el slug es "delivered"

## API Response

Las órdenes ahora incluyen el shipping_status en las respuestas API:

```json
{
  "uuid": "...",
  "order_number": "ORD-12345",
  "order_status": {
    "id": 2,
    "name": "Processing",
    "slug": "processing"
  },
  "shipping_status": {
    "id": 3,
    "name": "Enviado",
    "slug": "shipped",
    "color": "#8B5CF6",
    "sort_order": 3
  },
  ...
}
```

## Testing

### Crear un Estatus de Prueba

```php
use App\Models\ShippingStatus;

$status = ShippingStatus::create([
    'name' => 'En Aduana',
    'slug' => 'in_customs',
    'color' => '#EC4899',
    'is_active' => true,
    'sort_order' => 8,
]);
```

### Actualizar el Estatus de una Orden

```php
use App\Models\Order;
use App\Models\ShippingStatus;

$order = Order::where('order_number', 'ORD-12345')->first();
$status = ShippingStatus::where('slug', 'shipped')->first();

$order->setShippingStatus($status);
```

### Obtener Órdenes por Estatus de Envío

```php
$shippedOrders = Order::whereHas('shippingStatus', function($q) {
    $q->where('slug', 'shipped');
})->get();
```

## Troubleshooting

### Error: "Table 'shipping_statuses' doesn't exist"
**Solución:** Ejecuta `php artisan migrate`

### Error: "Column 'shipping_status_id' not found"
**Solución:** Ejecuta `php artisan migrate:fresh` (⚠️ Esto borrará todos los datos)

### No aparecen estatus en el dropdown
**Solución:** Verifica que existan estatus activos:
```bash
php artisan tinker
>>> \App\Models\ShippingStatus::active()->count()
```

### Las rutas no funcionan
**Solución:** Limpia la caché de rutas:
```bash
php artisan route:clear
php artisan route:cache
```

## Próximos Pasos

1. ✅ Módulo instalado y funcional
2. 🔄 Personalizar los estatus según tus necesidades
3. 🔄 Agregar notificaciones por email cuando cambie el estatus
4. 🔄 Integrar con servicios de tracking de envíos
5. 🔄 Agregar historial de cambios de estatus

## Soporte

Para más información, consulta:
- `SHIPPING_STATUS_MODULE.md` - Documentación completa
- Código fuente en `app/Models/ShippingStatus.php`
- Controladores en `app/Http/Controllers/Admin/`

## Notas Importantes

- ⚠️ No elimines el estatus marcado como "default"
- ⚠️ No puedes eliminar estatus que estén en uso por órdenes
- ⚠️ Al marcar un estatus como default, se activa automáticamente
- ✅ Los slugs deben ser únicos
- ✅ Los colores deben estar en formato hexadecimal (#RRGGBB)
- ✅ El campo `sort_order` determina el orden de visualización

## Compatibilidad

- Laravel 10.x ✅
- PHP 8.1+ ✅
- MySQL 5.7+ / PostgreSQL 12+ ✅
