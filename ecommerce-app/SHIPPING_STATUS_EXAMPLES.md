# Ejemplos de Uso - Módulo de Shipping Status

## Ejemplos Prácticos de Implementación

### 1. Crear un Nuevo Estatus de Envío

#### Desde el Panel Admin (UI)
1. Navega a `/admin/shipping-statuses`
2. Completa el formulario:
   - Nombre: "En Aduana"
   - Slug: "in_customs" (opcional, se genera automáticamente)
   - Color: "#EC4899"
   - Orden: 8
   - ✓ Activo
3. Clic en "Crear estatus"

#### Desde Código (Programáticamente)
```php
use App\Models\ShippingStatus;

$status = ShippingStatus::create([
    'name' => 'En Aduana',
    'slug' => 'in_customs',
    'color' => '#EC4899',
    'is_active' => true,
    'is_default' => false,
    'sort_order' => 8,
]);
```

#### Usando el Factory (Testing)
```php
use App\Models\ShippingStatus;

// Crear un estatus aleatorio
$status = ShippingStatus::factory()->create();

// Crear un estatus activo
$status = ShippingStatus::factory()->active()->create();

// Crear el estatus por defecto
$status = ShippingStatus::factory()->default()->create();
```

### 2. Actualizar el Estatus de una Orden

#### Desde el Panel Admin
1. Ve a `/admin/orders/{uuid}`
2. En la sección "Estatus de Envío"
3. Selecciona el nuevo estatus del dropdown
4. Clic en "Actualizar estatus de envío"

#### Desde Código
```php
use App\Models\Order;
use App\Models\ShippingStatus;

// Obtener la orden
$order = Order::where('order_number', 'ORD-12345')->first();

// Obtener el estatus
$status = ShippingStatus::where('slug', 'shipped')->first();

// Actualizar usando el método helper
$order->setShippingStatus($status);

// O actualizar directamente
$order->update(['shipping_status_id' => $status->id]);
```

### 3. Consultas Comunes

#### Obtener Todos los Estatus Activos
```php
use App\Models\ShippingStatus;

$activeStatuses = ShippingStatus::active()->get();
```

#### Obtener Estatus Ordenados
```php
$orderedStatuses = ShippingStatus::active()->ordered()->get();
```

#### Obtener el Estatus por Defecto
```php
$defaultStatus = ShippingStatus::default()->first();
```

#### Obtener Órdenes por Estatus de Envío
```php
use App\Models\Order;

// Órdenes enviadas
$shippedOrders = Order::whereHas('shippingStatus', function($q) {
    $q->where('slug', 'shipped');
})->get();

// Órdenes entregadas
$deliveredOrders = Order::whereHas('shippingStatus', function($q) {
    $q->where('slug', 'delivered');
})->get();

// Órdenes con estatus activo
$ordersWithActiveStatus = Order::whereHas('shippingStatus', function($q) {
    $q->where('is_active', true);
})->get();
```

#### Contar Órdenes por Estatus
```php
use App\Models\ShippingStatus;

$statuses = ShippingStatus::withCount('orders')->get();

foreach ($statuses as $status) {
    echo "{$status->name}: {$status->orders_count} órdenes\n";
}
```

### 4. Mostrar Estatus en Vistas Blade

#### Badge Simple
```blade
@if($order->shippingStatus)
    <span class="badge" style="background-color: {{ $order->shippingStatus->color }}">
        {{ $order->shippingStatus->name }}
    </span>
@endif
```

#### Badge con Punto de Color
```blade
@if($order->shippingStatus)
    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full" 
          style="background-color: {{ $order->shippingStatus->color }}22; color: {{ $order->shippingStatus->color }};">
        <span class="w-2 h-2 rounded-full mr-2" 
              style="background-color: {{ $order->shippingStatus->color }};"></span>
        {{ $order->shippingStatus->name }}
    </span>
@endif
```

#### Select de Estatus
```blade
<select name="shipping_status_id" class="form-select">
    <option value="">Seleccionar estatus...</option>
    @foreach(\App\Models\ShippingStatus::active()->ordered()->get() as $status)
        <option value="{{ $status->id }}" 
                {{ old('shipping_status_id', $order->shipping_status_id) == $status->id ? 'selected' : '' }}>
            {{ $status->name }}
        </option>
    @endforeach
</select>
```

#### Lista de Estatus con Colores
```blade
<div class="space-y-2">
    @foreach(\App\Models\ShippingStatus::ordered()->get() as $status)
        <div class="flex items-center justify-between p-3 border rounded">
            <div class="flex items-center">
                <span class="w-4 h-4 rounded-full mr-3" 
                      style="background-color: {{ $status->color }};"></span>
                <span class="font-medium">{{ $status->name }}</span>
            </div>
            <span class="text-sm text-gray-500">
                {{ $status->orders_count ?? 0 }} órdenes
            </span>
        </div>
    @endforeach
</div>
```

### 5. API Endpoints

#### Listar Todos los Estatus (JSON)
```bash
GET /admin/shipping-statuses
Accept: application/json
```

Respuesta:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Pendiente de Envío",
      "slug": "pending_shipment",
      "color": "#F59E0B",
      "is_active": true,
      "is_default": true,
      "sort_order": 1,
      "created_at": "2026-02-13T00:00:00Z",
      "updated_at": "2026-02-13T00:00:00Z"
    }
  ]
}
```

#### Crear Estatus (JSON)
```bash
POST /admin/shipping-statuses
Content-Type: application/json
Accept: application/json

{
  "name": "En Aduana",
  "slug": "in_customs",
  "color": "#EC4899",
  "is_active": true,
  "sort_order": 8
}
```

#### Actualizar Estatus de una Orden (JSON)
```bash
PATCH /admin/orders/{uuid}/shipping-status
Content-Type: application/json
Accept: application/json

{
  "shipping_status_id": 3
}
```

### 6. Eventos y Notificaciones

#### Escuchar Cambios de Estatus
```php
// En un Observer o Event Listener
use App\Models\Order;

Order::updated(function ($order) {
    if ($order->isDirty('shipping_status_id')) {
        $oldStatusId = $order->getOriginal('shipping_status_id');
        $newStatusId = $order->shipping_status_id;
        
        // Enviar notificación al cliente
        $order->user->notify(new ShippingStatusUpdated($order));
        
        // Registrar en log
        Log::info('Shipping status changed', [
            'order' => $order->order_number,
            'old_status' => $oldStatusId,
            'new_status' => $newStatusId,
        ]);
    }
});
```

### 7. Validaciones Personalizadas

#### Validar que el Estatus Esté Activo
```php
use Illuminate\Validation\Rule;

$request->validate([
    'shipping_status_id' => [
        'required',
        'integer',
        Rule::exists('shipping_statuses', 'id')->where(function ($query) {
            $query->where('is_active', true);
        }),
    ],
]);
```

### 8. Reportes y Estadísticas

#### Dashboard de Estatus
```php
use App\Models\ShippingStatus;
use Illuminate\Support\Facades\DB;

$statusStats = ShippingStatus::query()
    ->select('shipping_statuses.*')
    ->selectRaw('COUNT(orders.id) as orders_count')
    ->selectRaw('SUM(orders.total) as total_revenue')
    ->leftJoin('orders', 'shipping_statuses.id', '=', 'orders.shipping_status_id')
    ->groupBy('shipping_statuses.id')
    ->orderBy('sort_order')
    ->get();

foreach ($statusStats as $stat) {
    echo "{$stat->name}: {$stat->orders_count} órdenes, \${$stat->total_revenue}\n";
}
```

#### Órdenes por Estatus en un Rango de Fechas
```php
use Carbon\Carbon;

$startDate = Carbon::now()->subDays(30);
$endDate = Carbon::now();

$orders = Order::with('shippingStatus')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get()
    ->groupBy('shippingStatus.name');

foreach ($orders as $statusName => $orderGroup) {
    echo "{$statusName}: {$orderGroup->count()} órdenes\n";
}
```

### 9. Migraciones de Datos

#### Migrar Órdenes de un Estatus a Otro
```php
use App\Models\Order;
use App\Models\ShippingStatus;

$oldStatus = ShippingStatus::where('slug', 'old_status')->first();
$newStatus = ShippingStatus::where('slug', 'new_status')->first();

Order::where('shipping_status_id', $oldStatus->id)
    ->update(['shipping_status_id' => $newStatus->id]);
```

### 10. Testing

#### Test de Creación de Estatus
```php
use Tests\TestCase;
use App\Models\ShippingStatus;

class ShippingStatusTest extends TestCase
{
    public function test_can_create_shipping_status()
    {
        $status = ShippingStatus::factory()->create([
            'name' => 'Test Status',
            'slug' => 'test_status',
        ]);

        $this->assertDatabaseHas('shipping_statuses', [
            'name' => 'Test Status',
            'slug' => 'test_status',
        ]);
    }

    public function test_can_update_order_shipping_status()
    {
        $order = Order::factory()->create();
        $status = ShippingStatus::factory()->create();

        $order->setShippingStatus($status);

        $this->assertEquals($status->id, $order->fresh()->shipping_status_id);
    }
}
```

### 11. Comandos Artisan Personalizados

#### Comando para Sincronizar Estatus
```php
// app/Console/Commands/SyncShippingStatuses.php
namespace App\Console\Commands;

use App\Models\ShippingStatus;
use Illuminate\Console\Command;

class SyncShippingStatuses extends Command
{
    protected $signature = 'shipping:sync-statuses';
    protected $description = 'Sincroniza los estatus de envío con valores predefinidos';

    public function handle()
    {
        $statuses = [
            ['name' => 'Pendiente de Envío', 'slug' => 'pending_shipment', 'color' => '#F59E0B'],
            ['name' => 'Preparando', 'slug' => 'preparing', 'color' => '#3B82F6'],
            // ... más estatus
        ];

        foreach ($statuses as $index => $statusData) {
            ShippingStatus::updateOrCreate(
                ['slug' => $statusData['slug']],
                array_merge($statusData, ['sort_order' => $index + 1])
            );
        }

        $this->info('Estatus sincronizados correctamente.');
    }
}
```

Uso:
```bash
php artisan shipping:sync-statuses
```

### 12. Integración con Servicios Externos

#### Actualizar Estatus desde Webhook de Courier
```php
// En un controlador de webhook
public function handleCourierWebhook(Request $request)
{
    $trackingNumber = $request->input('tracking_number');
    $courierStatus = $request->input('status');

    // Mapear estatus del courier a nuestros estatus
    $statusMap = [
        'picked_up' => 'shipped',
        'in_transit' => 'in_transit',
        'delivered' => 'delivered',
    ];

    $slug = $statusMap[$courierStatus] ?? null;

    if ($slug) {
        $status = ShippingStatus::where('slug', $slug)->first();
        $order = Order::where('tracking_number', $trackingNumber)->first();

        if ($order && $status) {
            $order->setShippingStatus($status);
        }
    }
}
```

## Mejores Prácticas

1. **Siempre usa el método `setShippingStatus()`** en lugar de actualizar directamente el campo
2. **Valida que el estatus esté activo** antes de asignarlo
3. **Registra cambios en logs** para auditoría
4. **Usa scopes** para consultas comunes (`active()`, `ordered()`)
5. **Mantén los slugs consistentes** con tu lógica de negocio
6. **Usa colores consistentes** para mejor UX
7. **No elimines estatus en uso** - desactívalos en su lugar

## Recursos Adicionales

- Documentación completa: `SHIPPING_STATUS_MODULE.md`
- Guía de instalación: `SHIPPING_STATUS_INSTALLATION.md`
- Código fuente: `app/Models/ShippingStatus.php`
