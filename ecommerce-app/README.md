# 🛒 E-commerce Platform - Laravel & Tailwind CSS

## 📋 Descripción

Plataforma de e-commerce construida con **Laravel 11** y **Tailwind CSS**, implementando principios **SOLID** y patrones de diseño: **Repository**, **Strategy** y **Observer**.

### Stack Tecnológico

```
Backend:   Laravel 11.x
Frontend:  Tailwind CSS 3.x + Alpine.js
Database:  MySQL 8.0
Cache:     Redis
Search:    Meilisearch
Payments:  Stripe, PayPal
```

---

## 🏗️ Arquitectura y Patrones

### Principios SOLID

```php
// ✅ Single Responsibility - Un servicio, una responsabilidad
CartService          -> Gestiona operaciones del carrito
OrderService         -> Gestiona pedidos
PaymentService       -> Procesa pagos

// ✅ Dependency Inversion - Depender de abstracciones
public function __construct(
    private ProductRepositoryInterface $products,
    private CartRepositoryInterface $cart
) {}
```

### Patrones Implementados

#### 1. Repository Pattern
Abstrae el acceso a datos. Ubicación: `app/Repositories/`

```php
// Interface
interface ProductRepositoryInterface {
    public function all();
    public function find($id);
    public function findBySlug($slug);
}

// Implementación
class ProductRepository implements ProductRepositoryInterface {
    public function findBySlug($slug) {
        return $this->model
            ->with(['category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
```

#### 2. Strategy Pattern
Para pagos y envíos. Ubicación: `app/Services/*/Strategies/`

```php
// Interface
interface PaymentStrategyInterface {
    public function charge(Order $order): PaymentResult;
}

// Estrategias concretas
class StripePaymentStrategy implements PaymentStrategyInterface { }
class PayPalPaymentStrategy implements PaymentStrategyInterface { }
class CashOnDeliveryStrategy implements PaymentStrategyInterface { }

// Uso
$paymentService->setStrategy(new StripePaymentStrategy());
$result = $paymentService->processPayment($order);
```

#### 3. Observer Pattern
Para eventos de modelos. Ubicación: `app/Observers/`

```php
class OrderObserver {
    public function created(Order $order): void {
        // Reducir inventario
        // Enviar email confirmación
        // Notificar admin
        // Generar número de pedido
    }
    
    public function updated(Order $order): void {
        if ($order->isDirty('status')) {
            // Notificar cambio de estado
        }
    }
}
```

---

## 📁 Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Panel administración
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   └── ProductController.php
│   ├── Middleware/
│   └── Requests/               # Validaciones
│
├── Models/                     # Eloquent Models
│   ├── User.php
│   ├── Product.php
│   ├── Order.php
│   └── Cart.php
│
├── Repositories/
│   ├── Contracts/              # Interfaces
│   │   ├── ProductRepositoryInterface.php
│   │   ├── OrderRepositoryInterface.php
│   │   └── CartRepositoryInterface.php
│   └── Eloquent/               # Implementaciones
│       ├── ProductRepository.php
│       ├── OrderRepository.php
│       └── CartRepository.php
│
├── Services/                   # Lógica de negocio
│   ├── Cart/
│   │   └── CartService.php
│   ├── Order/
│   │   └── OrderService.php
│   ├── Payment/
│   │   ├── PaymentService.php
│   │   └── Strategies/
│   │       ├── PaymentStrategyInterface.php
│   │       ├── StripePaymentStrategy.php
│   │       └── PayPalPaymentStrategy.php
│   └── Shipping/
│       ├── ShippingService.php
│       └── Strategies/
│           ├── ShippingStrategyInterface.php
│           ├── StandardShippingStrategy.php
│           └── ExpressShippingStrategy.php
│
├── Observers/
│   ├── OrderObserver.php
│   ├── ProductObserver.php
│   └── UserObserver.php
│
├── Notifications/
│   ├── OrderConfirmation.php
│   └── OrderShipped.php
│
└── Providers/
    └── RepositoryServiceProvider.php
```

---

## 💾 Base de Datos

### Tablas Principales

```sql
users               # Usuarios del sistema
roles               # Roles (admin, customer)
categories          # Categorías de productos
products            # Catálogo de productos
product_images      # Imágenes de productos
product_variants    # Variantes (tallas, colores)
carts               # Carritos de compra
cart_items          # Items en el carrito
orders              # Pedidos
order_items         # Productos del pedido
addresses           # Direcciones de envío/facturación
payments            # Registro de pagos
coupons             # Cupones de descuento
reviews             # Reseñas de productos
```

### Relaciones Clave

```php
// User
$user->orders()
$user->cart()
$user->addresses()

// Product
$product->category()
$product->images()
$product->variants()
$product->reviews()

// Order
$order->user()
$order->items()
$order->payment()
$order->shippingAddress()
```

---

## 📦 Paquetes Esenciales

```bash
# Autenticación y Permisos
composer require laravel/breeze
composer require spatie/laravel-permission

# Imágenes
composer require spatie/laravel-medialibrary
composer require intervention/image

# Pagos
composer require laravel/cashier

# Búsqueda
composer require laravel/scout
composer require meilisearch/meilisearch-php

# PDF y Excel
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel

# Utilidades
composer require spatie/laravel-sitemap
composer require spatie/laravel-activitylog
composer require cviebrock/eloquent-sluggable

# Desarrollo
composer require laravel/telescope --dev
composer require barryvdh/laravel-debugbar --dev
```

---

## 🚀 Setup Inicial

```bash
# 1. Clonar e instalar dependencias
git clone <repo-url> && cd ecommerce
composer install && npm install

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar .env
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=

# 4. Migrar base de datos
php artisan migrate --seed

# 5. Crear storage link
php artisan storage:link

# 6. Compilar assets
npm run dev

# 7. Iniciar servidor
php artisan serve
```

---

## 📝 Convenciones de Código

### Nomenclatura

```php
// Clases: PascalCase
ProductController, OrderService, PaymentStrategyInterface

// Métodos y variables: camelCase
public function processPayment() {}
$userAddress, $cartTotal

// Constantes: UPPER_SNAKE_CASE
const MAX_ITEMS = 100;

// Rutas: kebab-case
/checkout/shipping-address

// Tablas y columnas: snake_case
products, order_items, created_at
```

### Estructura de Servicios

```php
<?php

namespace App\Services\Payment;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $repository,
        private NotificationService $notifications
    ) {}
    
    public function processPayment(Order $order): PaymentResult
    {
        DB::beginTransaction();
        try {
            $result = $this->strategy->charge($order);
            
            if ($result->success) {
                $this->recordPayment($order, $result);
                DB::commit();
                return $result;
            }
            
            DB::rollBack();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment failed", ['order' => $order->id]);
            throw new PaymentFailedException($e->getMessage());
        }
    }
}
```

---

## 🔧 Crear un Nuevo Módulo

### Ejemplo: Módulo de Wishlist

```bash
# 1. Migración
php artisan make:migration create_wishlists_table

# 2. Modelo
php artisan make:model Wishlist

# 3. Repository
# Crear: app/Repositories/Contracts/WishlistRepositoryInterface.php
# Crear: app/Repositories/Eloquent/WishlistRepository.php

# 4. Registrar en RepositoryServiceProvider
$this->app->bind(
    WishlistRepositoryInterface::class,
    WishlistRepository::class
);

# 5. Controlador
php artisan make:controller WishlistController

# 6. Rutas (web.php)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy']);
});

# 7. Vista
# Crear: resources/views/wishlist/index.blade.php
```

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test --filter CartTest

# Con coverage
php artisan test --coverage

# Ejemplo de test
public function test_user_can_add_product_to_cart(): void
{
    $user = User::factory()->create();
    $product = Product::factory()->create();
    
    $this->actingAs($user)
        ->post(route('cart.add'), ['product_id' => $product->id])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id
    ]);
}
```

---

## 🚢 Deployment

### Optimizaciones para Producción

```bash
# Cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Autoloader optimizado
composer install --optimize-autoloader --no-dev

# Assets
npm run build

# Permisos
chmod -R 755 storage bootstrap/cache

# Queue worker (Supervisor)
php artisan queue:work --daemon

# Cron (Scheduler)
* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

### Variables .env de Producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
```

---

## 📚 Comandos Útiles

```bash
# Limpiar cachés
php artisan optimize:clear

# Ver rutas
php artisan route:list

# Tinker (consola interactiva)
php artisan tinker

# Logs en tiempo real
tail -f storage/logs/laravel.log

# Regenerar autoload
composer dump-autoload

# Crear factory
php artisan make:factory ProductFactory

# Crear seeder
php artisan make:seeder ProductSeeder

# Crear observer
php artisan make:observer ProductObserver --model=Product
```

---

## 🎯 Flujo de Trabajo Git

```bash
# Crear rama de feature
git checkout -b feature/payment-integration

# Commits descriptivos
git commit -m "feat: add stripe payment strategy"
git commit -m "test: add payment service tests"
git commit -m "docs: update README"

# Push y crear PR
git push origin feature/payment-integration
```

---

## 📖 Guía Rápida para IA

### Al crear nuevos módulos:

1. **Siempre crear Repository** (Interface + Implementación)
2. **Registrar en RepositoryServiceProvider**
3. **Usar inyección de dependencias** en constructores
4. **Crear Observer** si el modelo tiene eventos importantes
5. **Escribir tests** para funcionalidad crítica
6. **Seguir nomenclatura** establecida
7. **Usar transacciones DB** para operaciones críticas
8. **Loggear errores** apropiadamente

### Estructura estándar de Controller:

```php
class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $products
    ) {}
    
    public function index()
    {
        $products = $this->products->all();
        return view('products.index', compact('products'));
    }
    
    public function store(StoreProductRequest $request)
    {
        $product = $this->products->create($request->validated());
        return redirect()->route('products.show', $product);
    }
}
```

### Estructura estándar de Service:

```php
class CartService
{
    public function __construct(
        private CartRepositoryInterface $cart,
        private ProductRepositoryInterface $products
    ) {}
    
    public function addItem(int $productId, int $quantity = 1): void
    {
        $product = $this->products->find($productId);
        
        if ($product->stock < $quantity) {
            throw new InsufficientStockException();
        }
        
        $this->cart->addItem($product, $quantity);
    }
}
```

---

## 📄 Licencia

Proyecto privado y confidencial.

---

**Versión:** 1.0.0  
**Última actualización:** Febrero 2026
