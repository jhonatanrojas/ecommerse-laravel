# Configuración de Emails del Carrito de Compra

## Descripción General

Los emails del carrito de compra están **deshabilitados por defecto** y deben ser habilitados explícitamente por el administrador del sistema. Esto permite un control total sobre qué notificaciones se envían a los usuarios.

## Tipos de Emails Disponibles

### 1. Email de Confirmación de Orden
Se envía automáticamente después de que un usuario completa exitosamente el checkout.

**Contenido sugerido:**
- Número de orden
- Resumen de productos comprados
- Totales (subtotal, descuento, impuestos, envío, total)
- Información de envío y facturación
- Método de pago
- Estado del pedido

### 2. Email de Carrito Abandonado
Se envía después de un período de tiempo configurable cuando un usuario crea un carrito pero no completa la compra.

**Contenido sugerido:**
- Recordatorio de productos en el carrito
- Enlaces directos al carrito
- Posible cupón de incentivo
- Fecha de expiración del carrito (para carritos de invitados)

## Configuración

### Variables de Entorno

Agrega las siguientes variables a tu archivo `.env`:

```env
# Habilitar/deshabilitar email de confirmación de orden (por defecto: false)
CART_EMAIL_ORDER_CONFIRMATION_ENABLED=false

# Habilitar/deshabilitar email de carrito abandonado (por defecto: false)
CART_EMAIL_CART_ABANDONMENT_ENABLED=false

# Retraso en horas antes de enviar email de carrito abandonado (por defecto: 24)
CART_EMAIL_ABANDONMENT_DELAY_HOURS=24
```

### Habilitar Emails

Para habilitar los emails, cambia los valores a `true`:

```env
# Habilitar email de confirmación de orden
CART_EMAIL_ORDER_CONFIRMATION_ENABLED=true

# Habilitar email de carrito abandonado
CART_EMAIL_CART_ABANDONMENT_ENABLED=true
```

### Configurar Retraso de Email de Abandono

Puedes ajustar cuántas horas esperar antes de enviar el email de carrito abandonado:

```env
# Enviar después de 48 horas
CART_EMAIL_ABANDONMENT_DELAY_HOURS=48

# Enviar después de 1 hora
CART_EMAIL_ABANDONMENT_DELAY_HOURS=1
```

## Implementación de Emails

Los listeners actuales solo registran en logs que se enviaría un email. Para implementar el envío real de emails:

### 1. Crear Mailables

```bash
php artisan make:mail OrderConfirmationMail
php artisan make:mail CartAbandonmentMail
```

### 2. Actualizar Listeners

En `app/Listeners/Cart/SendOrderConfirmationEmail.php`:

```php
use App\Mail\OrderConfirmationMail;

public function handle(CartCheckedOut $event): void
{
    if (!config('cart.emails.order_confirmation_enabled', false)) {
        return;
    }

    $order = $event->order;

    if ($order->user) {
        Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
    }
}
```

En `app/Listeners/Cart/SendCartAbandonmentEmail.php`:

```php
use App\Mail\CartAbandonmentMail;

public function handle(CartCreated $event): void
{
    if (!config('cart.emails.cart_abandonment_enabled', false)) {
        return;
    }

    $cart = $event->cart;

    // Verificar que el carrito aún existe y tiene items
    $cart = Cart::with('items')->find($cart->id);
    
    if (!$cart || $cart->items->isEmpty()) {
        return;
    }

    // Verificar que no se haya completado el checkout
    // (puedes agregar un campo 'checked_out_at' al modelo Cart)
    
    if ($cart->user) {
        Mail::to($cart->user->email)->send(new CartAbandonmentMail($cart));
    }
}
```

## Configuración de Cola (Queue)

Los listeners implementan `ShouldQueue`, lo que significa que se ejecutarán en segundo plano. Asegúrate de:

1. Configurar un driver de cola en `.env`:
```env
QUEUE_CONNECTION=redis
```

2. Ejecutar el worker de cola:
```bash
php artisan queue:work
```

## Panel de Administración

Para permitir que los administradores habiliten/deshabiliten estos emails desde un panel de administración:

### Opción 1: Tabla de Configuración en Base de Datos

Crear una tabla `settings` para almacenar configuraciones dinámicas:

```php
// Migration
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('string'); // string, boolean, integer, etc.
    $table->text('description')->nullable();
    $table->timestamps();
});

// Seeders con valores por defecto
DB::table('settings')->insert([
    [
        'key' => 'cart.emails.order_confirmation_enabled',
        'value' => 'false',
        'type' => 'boolean',
        'description' => 'Enviar email de confirmación de orden',
    ],
    [
        'key' => 'cart.emails.cart_abandonment_enabled',
        'value' => 'false',
        'type' => 'boolean',
        'description' => 'Enviar email de carrito abandonado',
    ],
]);
```

### Opción 2: Usar un Paquete de Configuración

Instalar un paquete como `spatie/laravel-settings` o `akaunting/laravel-setting` para gestionar configuraciones dinámicas.

### Opción 3: Archivo .env con Interfaz Admin

Crear una interfaz de administración que actualice el archivo `.env` directamente (menos recomendado para producción).

## Monitoreo

Los listeners registran eventos en los logs de Laravel. Puedes monitorear:

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Buscar eventos de email
grep "Order confirmation email" storage/logs/laravel.log
grep "Cart abandonment" storage/logs/laravel.log
```

## Seguridad y Mejores Prácticas

1. **Rate Limiting**: Implementa límites de tasa para prevenir spam
2. **Validación de Email**: Verifica que los emails sean válidos antes de enviar
3. **Unsubscribe**: Proporciona enlaces para darse de baja de emails de marketing
4. **GDPR Compliance**: Respeta las preferencias de privacidad del usuario
5. **Testing**: Usa `Mail::fake()` en tests para verificar que los emails se envían correctamente

## Ejemplo de Test

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

public function test_order_confirmation_email_sent_when_enabled()
{
    Mail::fake();
    config(['cart.emails.order_confirmation_enabled' => true]);

    // Realizar checkout...
    
    Mail::assertSent(OrderConfirmationMail::class);
}

public function test_order_confirmation_email_not_sent_when_disabled()
{
    Mail::fake();
    config(['cart.emails.order_confirmation_enabled' => false]);

    // Realizar checkout...
    
    Mail::assertNotSent(OrderConfirmationMail::class);
}
```

## Soporte

Para más información sobre el sistema de eventos de Laravel:
- [Laravel Events Documentation](https://laravel.com/docs/events)
- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Laravel Queues Documentation](https://laravel.com/docs/queues)
