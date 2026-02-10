# Módulo de Órdenes - Arquitectura SOLID

Este documento explica cómo cada componente del módulo de órdenes cumple con los principios SOLID.

---

## 📋 Estructura del Módulo

```
app/
├── Services/
│   ├── Contracts/
│   │   ├── OrderServiceInterface.php
│   │   └── OrderStatusServiceInterface.php
│   └── Order/
│       ├── OrderService.php
│       └── OrderStatusService.php
├── Repositories/
│   ├── Contracts/
│   │   └── OrderRepositoryInterface.php
│   └── Eloquent/
│       └── EloquentOrderRepository.php
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── OrderController.php
│   └── Requests/
│       └── Admin/
│           ├── FilterOrderRequest.php
│           └── UpdateOrderRequest.php
└── Providers/
    └── RepositoryServiceProvider.php

resources/views/admin/orders/
├── index.blade.php
├── show.blade.php
└── partials/
    ├── status-badge.blade.php
    └── payment-badge.blade.php

routes/
└── admin.php
```

---

## 🎯 Principios SOLID Aplicados

### **S - Single Responsibility Principle (SRP)**
*"Una clase debe tener una única razón para cambiar"*

#### ✅ Implementación:

1. **OrderController**
   - **Responsabilidad única**: Coordinar el flujo entre vistas y servicios
   - **NO contiene**: Lógica de negocio, validaciones, acceso a datos
   - **Solo hace**: Recibir requests, llamar servicios, retornar vistas

2. **OrderService**
   - **Responsabilidad única**: Lógica de negocio de órdenes
   - **Maneja**: Operaciones CRUD, cálculos, estadísticas
   - **NO maneja**: Cambios de estado (delegado a OrderStatusService)

3. **OrderStatusService**
   - **Responsabilidad única**: Gestión de estados de órdenes
   - **Maneja**: Transiciones de estado, validaciones de cambio, timestamps
   - **NO maneja**: Otras operaciones de órdenes

4. **EloquentOrderRepository**
   - **Responsabilidad única**: Acceso a datos de órdenes
   - **Maneja**: Queries, filtros, relaciones Eloquent
   - **NO maneja**: Lógica de negocio

5. **FilterOrderRequest / UpdateOrderRequest**
   - **Responsabilidad única**: Validación de datos de entrada
   - **Maneja**: Reglas de validación, mensajes de error
   - **NO maneja**: Lógica de negocio

---

### **O - Open/Closed Principle (OCP)**
*"Abierto para extensión, cerrado para modificación"*

#### ✅ Implementación:

1. **OrderStatusService - Matriz de Transiciones**
   ```php
   protected array $allowedTransitions = [
       OrderStatus::Pending->value => [
           OrderStatus::Processing->value,
           OrderStatus::Cancelled->value,
       ],
       // ...
   ];
   ```
   - **Extensión**: Agregar nuevas transiciones modificando solo el array
   - **Sin modificar**: La lógica de `canChangeStatus()` permanece intacta

2. **Inyección de Dependencias**
   - Nuevas funcionalidades (tracking, facturación) se agregan mediante nuevos servicios
   - No se modifica el código existente, solo se inyectan nuevas dependencias

3. **Interfaces**
   - Permiten agregar nuevas implementaciones sin modificar el código que las usa
   - Ejemplo: Agregar `CachedOrderRepository` sin cambiar `OrderService`

---

### **L - Liskov Substitution Principle (LSP)**
*"Los objetos de una clase derivada deben poder reemplazar objetos de la clase base"*

#### ✅ Implementación:

1. **Repositorios Intercambiables**
   ```php
   // Se puede reemplazar EloquentOrderRepository por cualquier implementación
   $this->app->bind(
       OrderRepositoryInterface::class,
       EloquentOrderRepository::class // O CachedOrderRepository, ApiOrderRepository, etc.
   );
   ```

2. **Servicios Intercambiables**
   ```php
   // OrderService puede ser reemplazado por OrderServiceWithCache
   $this->app->bind(
       OrderServiceInterface::class,
       OrderService::class
   );
   ```

3. **Contratos Respetados**
   - Todas las implementaciones respetan los contratos de sus interfaces
   - Los tipos de retorno y parámetros son consistentes

---

### **I - Interface Segregation Principle (ISP)**
*"Los clientes no deben depender de interfaces que no usan"*

#### ✅ Implementación:

1. **Interfaces Específicas y Pequeñas**

   **OrderServiceInterface** - Solo operaciones de órdenes:
   ```php
   interface OrderServiceInterface {
       public function getPaginatedOrders(...);
       public function getOrderByUuid(...);
       public function updateOrder(...);
       public function cancelOrder(...);
       public function getOrderStatistics();
   }
   ```

   **OrderStatusServiceInterface** - Solo operaciones de estado:
   ```php
   interface OrderStatusServiceInterface {
       public function changeStatus(...);
       public function canChangeStatus(...);
       public function getAvailableStatuses(...);
       public function getAllStatuses();
   }
   ```

2. **Separación de Responsabilidades**
   - El controlador no necesita métodos de estado si solo lista órdenes
   - El servicio de estado no necesita métodos de cálculo de totales
   - Cada interfaz expone solo lo necesario para su propósito

---

### **D - Dependency Inversion Principle (DIP)**
*"Depender de abstracciones, no de implementaciones concretas"*

#### ✅ Implementación:

1. **OrderController - Depende de Interfaces**
   ```php
   public function __construct(
       protected OrderServiceInterface $orderService,        // ✅ Interfaz
       protected OrderStatusServiceInterface $statusService  // ✅ Interfaz
   ) {}
   ```
   - **NO depende de**: `OrderService`, `OrderStatusService` (implementaciones)
   - **Depende de**: Interfaces (abstracciones)

2. **OrderService - Depende de Interfaces**
   ```php
   public function __construct(
       protected OrderRepositoryInterface $repository,       // ✅ Interfaz
       protected OrderStatusServiceInterface $statusService  // ✅ Interfaz
   ) {}
   ```

3. **Binding en Service Provider**
   ```php
   $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
   $this->app->bind(OrderServiceInterface::class, OrderService::class);
   $this->app->bind(OrderStatusServiceInterface::class, OrderStatusService::class);
   ```
   - Laravel resuelve automáticamente las dependencias
   - Fácil cambiar implementaciones sin modificar código

---

## 🔄 Flujo de Datos (Arquitectura en Capas)

```
┌─────────────────────────────────────────────────────────────┐
│                         VISTA (Blade)                        │
│                    index.blade.php, show.blade.php           │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLADOR (Coordinator)                 │
│                      OrderController                         │
│  - Recibe requests                                           │
│  - Llama servicios                                           │
│  - Retorna vistas                                            │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                   VALIDACIÓN (Requests)                      │
│            FilterOrderRequest, UpdateOrderRequest            │
│  - Valida datos de entrada                                   │
│  - Retorna errores o datos limpios                           │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                  SERVICIOS (Business Logic)                  │
│              OrderService, OrderStatusService                │
│  - Lógica de negocio                                         │
│  - Transacciones                                             │
│  - Auditoría                                                 │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                  REPOSITORIO (Data Access)                   │
│                  EloquentOrderRepository                     │
│  - Queries a la base de datos                                │
│  - Filtros y relaciones                                      │
│  - Retorna modelos Eloquent                                  │
└─────────────────────────────────────────────────────────────┘
                              ↕
┌─────────────────────────────────────────────────────────────┐
│                      MODELO (Eloquent)                       │
│                      Order, OrderItem                        │
│  - Representación de datos                                   │
│  - Relaciones                                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 🚀 Ventajas de esta Arquitectura

### 1. **Testabilidad**
- Cada componente se puede testear de forma aislada
- Fácil crear mocks de interfaces para tests unitarios

### 2. **Mantenibilidad**
- Cambios en una capa no afectan a las demás
- Código organizado y fácil de entender

### 3. **Escalabilidad**
- Agregar nuevas funcionalidades sin modificar código existente
- Ejemplo: Agregar tracking de envíos creando `OrderTrackingService`

### 4. **Reutilización**
- Los servicios pueden ser usados desde API, comandos Artisan, jobs, etc.
- No están acoplados a HTTP

### 5. **Flexibilidad**
- Cambiar implementaciones fácilmente (Eloquent → API, Cache, etc.)
- Agregar decoradores, observers, eventos sin modificar código base

---

## 📝 Ejemplos de Extensión (Sin Modificar Código)

### Agregar Cache
```php
class CachedOrderRepository implements OrderRepositoryInterface {
    public function __construct(
        protected EloquentOrderRepository $repository,
        protected CacheInterface $cache
    ) {}
    
    public function findByUuid(string $uuid): ?Order {
        return $this->cache->remember("order.$uuid", fn() => 
            $this->repository->findByUuid($uuid)
        );
    }
}
```

### Agregar Tracking
```php
class OrderTrackingService {
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected ShippingApiInterface $shippingApi
    ) {}
    
    public function getTrackingInfo(Order $order): array {
        return $this->shippingApi->track($order->tracking_number);
    }
}
```

### Agregar Facturación
```php
class OrderInvoiceService {
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected InvoiceGeneratorInterface $generator
    ) {}
    
    public function generateInvoice(Order $order): Invoice {
        return $this->generator->create($order);
    }
}
```

---

## ✅ Checklist de Cumplimiento SOLID

- [x] **SRP**: Cada clase tiene una única responsabilidad
- [x] **OCP**: Abierto para extensión mediante inyección de dependencias
- [x] **LSP**: Implementaciones intercambiables respetando contratos
- [x] **ISP**: Interfaces pequeñas y específicas
- [x] **DIP**: Dependencias de abstracciones, no implementaciones

---

## 🎓 Conclusión

Este módulo de órdenes es un ejemplo completo de arquitectura limpia aplicando SOLID:

- **Desacoplado**: Cada componente es independiente
- **Testeable**: Fácil crear tests unitarios y de integración
- **Escalable**: Agregar funcionalidades sin modificar código existente
- **Mantenible**: Código organizado y fácil de entender
- **Profesional**: Sigue las mejores prácticas de la industria

El módulo está listo para producción y puede ser extendido con tracking, facturación, notificaciones, webhooks, etc., sin modificar el código base.
