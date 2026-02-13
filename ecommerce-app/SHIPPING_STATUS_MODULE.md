# Módulo de Gestión de Estatus de Envíos

## Descripción General

Este módulo permite gestionar dinámicamente los estatus de envío de las órdenes desde el panel administrativo, reemplazando el sistema anterior basado en enums por un sistema flexible y administrable.

## Estructura del Módulo

### 1. Base de Datos

#### Tabla: `shipping_statuses`
- `id` - Identificador único
- `name` - Nombre del estatus (ej: "Enviado")
- `slug` - Identificador único en formato slug (ej: "shipped")
- `color` - Color hexadecimal para badges en UI (ej: "#8B5CF6")
- `is_active` - Indica si el estatus está activo
- `is_default` - Indica si es el estatus por defecto
- `sort_order` - Orden de visualización
- `timestamps` - Fechas de creación y actualización

#### Relación con Orders
- Se agregó `shipping_status_id` a la tabla `orders`
- Relación: `orders.shipping_status_id` → `shipping_statuses.id`
- Restricción: `ON DELETE RESTRICT` (no se puede eliminar un estatus en uso)

### 2. Modelos

#### ShippingStatus (`app/Models/ShippingStatus.php`)
```php
// Relaciones
orders() // HasMany - Órdenes con este estatus

// Scopes
active() // Solo estatus activos
default() // Estatus por defecto
ordered() // Ordenados por sort_order
```

#### Order (actualizado)
```php
// Nueva relación
shippingStatus() // BelongsTo ShippingStatus

// Nuevo método helper
setShippingStatus(ShippingStatus $status) // Actualiza el estatus de envío
```

### 3. Controladores

#### AdminShippingStatusController
Gestiona el CRUD de estatus de envío:
- `index()` - Lista todos los estatus
- `store()` - Crea un nuevo estatus
- `update()` - Actualiza un estatus existente
- `toggleStatus()` - Activa/desactiva un estatus
- `setDefault()` - Marca un estatus como predeterminado
- `destroy()` - Elimina un estatus (si no está en uso)

#### AdminOrderShippingStatusUpdateController
Actualiza el estatus de envío desde la vista de orden:
- `update($orderUuid)` - Actualiza el shipping_status_id de una orden
- Valida que el estatus esté activo
- Registra auditoría en logs
- Actualiza timestamps relacionados (shipped_at, delivered_at)

### 4. Requests (Validación)

#### StoreShippingStatusRequest
Valida la creación de nuevos estatus:
- `name` - Requerido, máx 255 caracteres
- `slug` - Opcional (se genera automáticamente), único
- `color` - Opcional, formato hexadecimal (#RRGGBB)
- `is_active` - Opcional, booleano (default: true)
- `is_default` - Opcional, booleano (default: false)
- `sort_order` - Opcional, entero >= 0 (default: 0)

#### UpdateShippingStatusRequest
Similar a Store, pero permite actualizar estatus existentes.

#### UpdateOrderShippingStatusRequest
Valida la actualización del estatus desde una orden:
- `shipping_status_id` - Requerido, debe existir en shipping_statuses

### 5. Resources (API)

#### ShippingStatusResource
Formatea los datos del estatus para respuestas API/JSON:
```json
{
  "id": 1,
  "name": "Enviado",
  "slug": "shipped",
  "color": "#8B5CF6",
  "is_active": true,
  "is_default": false,
  "sort_order": 3,
  "created_at": "2026-02-13T00:00:00Z",
  "updated_at": "2026-02-13T00:00:00Z"
}
```

### 6. Rutas (routes/admin.php)

```php
// Gestión de estatus de envío
GET    /admin/shipping-statuses           // Listar
POST   /admin/shipping-statuses           // Crear
PUT    /admin/shipping-statuses/{id}      // Actualizar
PATCH  /admin/shipping-statuses/{id}/toggle   // Activar/Desactivar
PATCH  /admin/shipping-statuses/{id}/default  // Marcar como default
DELETE /admin/shipping-statuses/{id}      // Eliminar

// Actualizar estatus desde orden
PATCH  /admin/orders/{uuid}/shipping-status   // Actualizar estatus de envío
```

### 7. Seeder

#### ShippingStatusSeeder
Crea los estatus iniciales:
1. **Pendiente de Envío** (pending_shipment) - #F59E0B - Default
2. **Preparando** (preparing) - #3B82F6
3. **Enviado** (shipped) - #8B5CF6
4. **En Tránsito** (in_transit) - #06B6D4
5. **Entregado** (delivered) - #10B981
6. **Devuelto** (returned) - #F97316
7. **Cancelado** (cancelled) - #EF4444

## Instalación y Configuración

### 1. Ejecutar la migración
```bash
php artisan migrate
```

La migración automáticamente:
- Crea la tabla `shipping_statuses`
- Inserta los estatus iniciales
- Agrega la columna `shipping_status_id` a `orders`
- Asigna el estatus por defecto a todas las órdenes existentes

### 2. (Opcional) Ejecutar el seeder manualmente
```bash
php artisan db:seed --class=ShippingStatusSeeder
```

## Uso en el Panel Administrativo

### Gestión de Estatus de Envío

#### Listar Estatus
Accede a `/admin/shipping-statuses` para ver todos los estatus configurados.

#### Crear Nuevo Estatus
```php
POST /admin/shipping-statuses
{
  "name": "En Aduana",
  "slug": "in_customs",
  "color": "#EC4899",
  "is_active": true,
  "sort_order": 4
}
```

#### Actualizar Estatus
```php
PUT /admin/shipping-statuses/{id}
{
  "name": "En Tránsito Internacional",
  "color": "#06B6D4",
  "sort_order": 5
}
```

#### Activar/Desactivar
```php
PATCH /admin/shipping-statuses/{id}/toggle
```

#### Marcar como Default
```php
PATCH /admin/shipping-statuses/{id}/default
```

#### Eliminar
```php
DELETE /admin/shipping-statuses/{id}
```
**Nota:** No se puede eliminar un estatus que esté en uso o sea el default.

### Actualizar Estatus desde Orden

En la vista de detalle de orden (`/admin/orders/{uuid}`):

```php
PATCH /admin/orders/{uuid}/shipping-status
{
  "shipping_status_id": 3
}
```

El sistema automáticamente:
- Valida que el estatus esté activo
- Actualiza el `shipping_status_id` de la orden
- Registra la auditoría en logs
- Actualiza `shipped_at` si el slug es "shipped"
- Actualiza `delivered_at` si el slug es "delivered"

## Integración con Frontend

### Mostrar Badge de Estatus
```blade
@if($order->shippingStatus)
    <span class="badge" style="background-color: {{ $order->shippingStatus->color }}">
        {{ $order->shippingStatus->name }}
    </span>
@endif
```

### Select de Estatus Activos
```blade
<select name="shipping_status_id">
    @foreach(\App\Models\ShippingStatus::active()->ordered()->get() as $status)
        <option value="{{ $status->id }}" 
                {{ $order->shipping_status_id == $status->id ? 'selected' : '' }}>
            {{ $status->name }}
        </option>
    @endforeach
</select>
```

## API Response

El `OrderResource` ahora incluye el shipping_status:

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
    "color": "#8B5CF6"
  },
  "payment_status": "paid",
  ...
}
```

## Reglas de Negocio

1. **Estatus Default**: Siempre debe existir un estatus marcado como default
2. **No Eliminar en Uso**: No se puede eliminar un estatus que tenga órdenes asociadas
3. **No Desactivar Default**: No se puede desactivar el estatus por defecto
4. **Auto-activación**: Al marcar un estatus como default, se activa automáticamente
5. **Slug Único**: Cada estatus debe tener un slug único
6. **Timestamps Automáticos**: Los campos `shipped_at` y `delivered_at` se actualizan automáticamente según el slug

## Auditoría

Cada cambio de estatus de envío se registra en los logs con:
- UUID de la orden
- Número de orden
- Estatus anterior
- Estatus nuevo
- Usuario que realizó el cambio

## Mantenimiento

### Agregar Nuevos Estatus
Simplemente usa el panel admin o ejecuta:
```php
ShippingStatus::create([
    'name' => 'Nuevo Estatus',
    'slug' => 'nuevo_estatus',
    'color' => '#FF5733',
    'is_active' => true,
    'sort_order' => 10,
]);
```

### Reordenar Estatus
Actualiza el campo `sort_order` de cada estatus según el orden deseado.

## Compatibilidad

Este módulo es compatible con:
- Laravel 10.x
- PHP 8.1+
- MySQL 5.7+ / PostgreSQL 12+

## Notas Técnicas

- El módulo sigue los principios SOLID
- Usa Repository Pattern para acceso a datos
- Implementa validación robusta con Form Requests
- Incluye auditoría automática
- Soporta respuestas JSON y vistas Blade
- Mantiene coherencia con el flujo de órdenes existente
