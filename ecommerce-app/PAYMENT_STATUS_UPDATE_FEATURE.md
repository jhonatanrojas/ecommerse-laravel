# Funcionalidad de Actualización de Estado de Pago

## Descripción
Se ha implementado la funcionalidad para actualizar el estado del pago directamente desde el panel administrativo en la vista de detalle de la orden (`/admin/orders/{uuid}`).

## Componentes Implementados

### 1. Servicio de Estado de Pago
**Archivo**: `app/Services/Payments/PaymentStatusService.php`

Servicio dedicado a la gestión de estados de pagos siguiendo principios SOLID:
- Gestiona transiciones de estado permitidas
- Registra auditoría de cambios (usuario admin, timestamp, notas)
- Sincroniza automáticamente con el estado de pago de la orden
- Actualiza fechas de pago/reembolso según el estado

**Estados permitidos**:
- `pending` → `completed`, `failed`
- `completed` → `refunded`, `partially_refunded`
- `failed` → `pending`
- `partially_refunded` → `refunded`
- `refunded` → (sin transiciones)

### 2. Interfaz del Servicio
**Archivo**: `app/Services/Contracts/PaymentStatusServiceInterface.php`

Define el contrato para operaciones de estado de pagos:
- `changeStatus()`: Cambiar estado del pago
- `canChangeStatus()`: Validar si el cambio es permitido
- `getAvailableStatuses()`: Obtener estados disponibles
- `getAllStatuses()`: Obtener todos los estados posibles

### 3. Request de Validación
**Archivo**: `app/Http/Requests/Admin/UpdatePaymentStatusRequest.php`

Valida los datos de entrada:
- `status`: Requerido, debe ser un estado válido
- `admin_note`: Opcional, máximo 500 caracteres

### 4. Controlador
**Archivo**: `app/Http/Controllers/Admin/OrderController.php`

Métodos actualizados:
- `show()`: Carga el pago más reciente y estados disponibles
- `updatePaymentStatus()`: Procesa la actualización del estado

### 5. Vista
**Archivo**: `resources/views/admin/orders/show.blade.php`

Sección de "Estado de Pago" actualizada con:
- Visualización del estado actual del pago
- ID de transacción (si existe)
- Fecha de pago (si existe)
- Fecha de reembolso (si existe)
- Monto reembolsado (si existe)
- Selector para cambiar estado
- Campo de nota opcional
- Confirmaciones JavaScript para estados críticos

### 6. Partial de Badge
**Archivo**: `resources/views/admin/orders/partials/payment-record-badge.blade.php`

Badge visual para mostrar el estado del pago con colores:
- Pendiente: Amarillo
- Completado: Verde
- Fallido: Rojo
- Reembolsado: Púrpura
- Reembolso Parcial: Azul

### 7. Ruta
**Archivo**: `routes/admin.php`

Nueva ruta agregada:
```php
Route::patch('orders/{uuid}/payment-status', [OrderController::class, 'updatePaymentStatus'])
    ->name('admin.orders.payment-status.update');
```

### 8. Service Provider
**Archivo**: `app/Providers/AppServiceProvider.php`

Binding registrado:
```php
$this->app->bind(
    PaymentStatusServiceInterface::class,
    PaymentStatusService::class
);
```

## Características de UX

### Confirmaciones
- Al cambiar a "completed": Confirma que se actualizará el estado de la orden
- Al cambiar a "refunded" o "partially_refunded": Confirma que el reembolso fue procesado en la pasarela

### Auditoría
Cada cambio de estado registra en `gateway_response`:
```json
{
  "admin_updates": [
    {
      "timestamp": "2026-02-12T10:30:00Z",
      "user": "Admin Name",
      "user_uuid": "uuid-here",
      "old_status": "pending",
      "new_status": "completed",
      "note": "Pago verificado manualmente"
    }
  ]
}
```

### Sincronización
El estado del pago se sincroniza automáticamente con `Order.payment_status`:
- `PaymentRecordStatus::Completed` → `PaymentStatus::Paid`
- `PaymentRecordStatus::Failed` → `PaymentStatus::Failed`
- `PaymentRecordStatus::Refunded` → `PaymentStatus::Refunded`
- `PaymentRecordStatus::PartiallyRefunded` → `PaymentStatus::PartiallyRefunded`
- `PaymentRecordStatus::Pending` → `PaymentStatus::Pending`

## Uso

1. Navegar a `/admin/orders/{uuid}`
2. En la columna lateral, ver la sección "Estado de Pago"
3. Seleccionar el nuevo estado del dropdown
4. Opcionalmente agregar una nota explicativa
5. Hacer clic en "Actualizar Estado de Pago"
6. Confirmar la acción si es un estado crítico

## Validaciones

- Solo se pueden realizar transiciones permitidas según la matriz de estados
- No se puede cambiar al mismo estado actual
- Se valida que el pago exista antes de actualizar
- Se registra en logs cualquier intento de cambio no permitido

## Logs

El servicio registra:
- Cambios exitosos de estado (nivel INFO)
- Intentos de cambios no permitidos (nivel WARNING)
- Errores durante el proceso (nivel ERROR)

## Arquitectura SOLID

La implementación sigue principios SOLID:
- **SRP**: Cada clase tiene una responsabilidad única
- **OCP**: Abierto para extensión mediante inyección de dependencias
- **LSP**: Las interfaces definen contratos claros
- **ISP**: Interfaces específicas para cada funcionalidad
- **DIP**: Dependencia de abstracciones, no de implementaciones concretas
