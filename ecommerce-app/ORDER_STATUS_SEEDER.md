# Seeder de Estatus de Órdenes

## Archivos Creados

- ✅ `database/seeders/OrderStatusSeeder.php` - Seeder con estatus predefinidos
- ✅ `database/factories/OrderStatusFactory.php` - Factory para testing

## Estatus Predefinidos

El seeder crea los siguientes estatus de orden:

| Nombre | Slug | Color | Default |
|--------|------|-------|---------|
| Pendiente | pending | #F59E0B (Amarillo) | ✅ Sí |
| Procesando | processing | #3B82F6 (Azul) | No |
| Enviado | shipped | #8B5CF6 (Púrpura) | No |
| Entregado | delivered | #10B981 (Verde) | No |
| Cancelado | cancelled | #EF4444 (Rojo) | No |
| Reembolsado | refunded | #EC4899 (Rosa) | No |
| Fallido | failed | #DC2626 (Rojo oscuro) | No |

## Uso

### Ejecutar el Seeder

```bash
# Ejecutar solo el seeder de OrderStatus
php artisan db:seed --class=OrderStatusSeeder

# O ejecutar todos los seeders (si está incluido en DatabaseSeeder)
php artisan db:seed
```

### Ejecutar con Migración Fresh

```bash
# Resetear base de datos y ejecutar seeders
php artisan migrate:fresh --seed
```

### Verificar que se Crearon los Estatus

```bash
php artisan tinker
>>> \App\Models\OrderStatus::count()
# Debería retornar: 7

>>> \App\Models\OrderStatus::pluck('name', 'slug')
# Muestra todos los estatus creados
```

## Integración con DatabaseSeeder

Para que se ejecute automáticamente con `php artisan db:seed`, agrégalo al `DatabaseSeeder`:

```php
// database/seeders/DatabaseSeeder.php

public function run(): void
{
    $this->call([
        // ... otros seeders
        OrderStatusSeeder::class,
        ShippingStatusSeeder::class,
        // ... más seeders
    ]);
}
```

## Uso del Factory

### En Tests

```php
use App\Models\OrderStatus;

// Crear un estatus aleatorio
$status = OrderStatus::factory()->create();

// Crear un estatus activo
$status = OrderStatus::factory()->active()->create();

// Crear el estatus por defecto
$status = OrderStatus::factory()->default()->create();

// Crear múltiples estatus
$statuses = OrderStatus::factory()->count(5)->create();

// Crear con atributos específicos
$status = OrderStatus::factory()->create([
    'name' => 'En Revisión',
    'slug' => 'under_review',
    'color' => '#6366F1',
]);
```

### En Seeders de Testing

```php
// database/seeders/TestingSeeder.php
public function run(): void
{
    // Crear 10 estatus de prueba
    OrderStatus::factory()->count(10)->create();
    
    // Asegurar que hay un estatus por defecto
    OrderStatus::factory()->default()->create();
}
```

## Personalización

### Agregar Más Estatus

Edita `database/seeders/OrderStatusSeeder.php` y agrega más estatus al array:

```php
$statuses = [
    // ... estatus existentes
    [
        'name' => 'En Revisión',
        'slug' => 'under_review',
        'color' => '#6366F1',
        'is_active' => true,
        'is_default' => false,
    ],
    [
        'name' => 'Confirmado',
        'slug' => 'confirmed',
        'color' => '#14B8A6',
        'is_active' => true,
        'is_default' => false,
    ],
];
```

### Cambiar el Estatus por Defecto

Modifica el array en el seeder para marcar otro estatus como default:

```php
[
    'name' => 'Procesando',
    'slug' => 'processing',
    'color' => '#3B82F6',
    'is_active' => true,
    'is_default' => true, // Cambiar a true
],
```

**Nota:** Solo debe haber un estatus marcado como `is_default => true`.

## Colores Recomendados

Paleta de colores Tailwind CSS para estatus:

| Color | Hex | Uso Recomendado |
|-------|-----|-----------------|
| Amarillo | #F59E0B | Pendiente, En espera |
| Azul | #3B82F6 | Procesando, En progreso |
| Púrpura | #8B5CF6 | Enviado, En tránsito |
| Verde | #10B981 | Completado, Entregado |
| Rojo | #EF4444 | Cancelado, Error |
| Rosa | #EC4899 | Reembolsado |
| Gris | #6B7280 | Inactivo, Archivado |
| Índigo | #6366F1 | En revisión |
| Teal | #14B8A6 | Confirmado |

## Migración de Datos Existentes

Si ya tienes órdenes en la base de datos, el seeder usará `updateOrCreate` para no duplicar estatus. Las órdenes existentes mantendrán sus referencias.

### Asignar Estatus a Órdenes Existentes

```php
use App\Models\Order;
use App\Models\OrderStatus;

// Obtener el estatus por defecto
$defaultStatus = OrderStatus::where('is_default', true)->first();

// Asignar a órdenes sin estatus
Order::whereNull('order_status_id')->update([
    'order_status_id' => $defaultStatus->id
]);
```

## Troubleshooting

### Error: "Duplicate entry for key 'slug'"
**Causa:** Ya existe un estatus con ese slug.
**Solución:** El seeder usa `updateOrCreate`, así que esto no debería pasar. Si ocurre, verifica que no haya duplicados manuales.

### Error: "Multiple default statuses"
**Causa:** Hay más de un estatus marcado como default.
**Solución:** 
```bash
php artisan tinker
>>> \App\Models\OrderStatus::where('is_default', true)->update(['is_default' => false])
>>> \App\Models\OrderStatus::where('slug', 'pending')->update(['is_default' => true])
```

### No se Crean los Estatus
**Solución:** Verifica que la migración de order_statuses se haya ejecutado:
```bash
php artisan migrate:status
```

## Comandos Útiles

```bash
# Ver todos los estatus
php artisan tinker
>>> \App\Models\OrderStatus::all()

# Ver solo estatus activos
>>> \App\Models\OrderStatus::active()->get()

# Ver el estatus por defecto
>>> \App\Models\OrderStatus::default()->first()

# Contar órdenes por estatus
>>> \App\Models\OrderStatus::withCount('orders')->get()

# Desactivar un estatus
>>> \App\Models\OrderStatus::where('slug', 'failed')->update(['is_active' => false])
```

## Ejemplo Completo de Uso

```php
// 1. Ejecutar el seeder
php artisan db:seed --class=OrderStatusSeeder

// 2. Crear una orden con el estatus por defecto
use App\Models\Order;
use App\Models\OrderStatus;

$defaultStatus = OrderStatus::default()->first();

$order = Order::create([
    'user_id' => 1,
    'order_number' => 'ORD-' . time(),
    'order_status_id' => $defaultStatus->id,
    'status' => $defaultStatus->slug,
    'total' => 100.00,
    // ... otros campos
]);

// 3. Actualizar el estatus de la orden
$processingStatus = OrderStatus::where('slug', 'processing')->first();
$order->setStatus($processingStatus);

// 4. Verificar el cambio
echo $order->orderStatus->name; // "Procesando"
```

## Notas Importantes

- ✅ El seeder es idempotente (puede ejecutarse múltiples veces sin duplicar)
- ✅ Usa `updateOrCreate` para actualizar estatus existentes
- ✅ Mantiene las relaciones con órdenes existentes
- ✅ Los slugs deben coincidir con los valores del enum `OrderStatus` para compatibilidad
- ⚠️ Solo debe haber un estatus marcado como `is_default`
- ⚠️ No elimines estatus que estén en uso por órdenes

## Relación con ShippingStatus

Ambos seeders pueden ejecutarse juntos:

```bash
php artisan db:seed --class=OrderStatusSeeder
php artisan db:seed --class=ShippingStatusSeeder
```

O agregarlos al `DatabaseSeeder` para ejecutarlos automáticamente:

```php
public function run(): void
{
    $this->call([
        OrderStatusSeeder::class,
        ShippingStatusSeeder::class,
    ]);
}
```
