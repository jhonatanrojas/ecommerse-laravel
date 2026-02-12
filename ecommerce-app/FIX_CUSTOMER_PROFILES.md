# Corrección: Perfil de Cliente Automático

## Problema Resuelto

El error "Usuario no tiene perfil de cliente" ocurría porque al registrarse un usuario, no se creaba automáticamente su perfil de cliente (`Customer`).

## Cambios Realizados

### 1. AuthService.php
Se modificó el método `register()` para crear automáticamente el perfil de cliente cuando se registra un usuario:

```php
// Crear perfil de cliente automáticamente
$user->customer()->create([
    'phone' => $data['phone'] ?? null,
]);
```

### 2. RegisteredUserController.php
Se modificó el método `store()` para crear el perfil de cliente en el registro tradicional:

```php
// Crear perfil de cliente automáticamente
$user->customer()->create([
    'phone' => $request->phone,
]);
```

### 3. RegisterRequest.php
Se agregó el campo `phone` a las validaciones:

```php
'phone' => ['nullable', 'string', 'max:20'],
```

### 4. Comando Artisan para Usuarios Existentes
Se creó el comando `CreateMissingCustomerProfiles` para corregir usuarios existentes sin perfil de cliente.

## Instrucciones para Aplicar la Corrección

### Para Usuarios Existentes

Si ya tienes usuarios registrados sin perfil de cliente, ejecuta el siguiente comando:

```bash
php artisan customers:create-missing-profiles
```

Este comando:
- Busca todos los usuarios que no tienen perfil de cliente
- Crea automáticamente el perfil de cliente para cada uno
- Muestra una barra de progreso durante el proceso

### Para Nuevos Usuarios

Los nuevos usuarios que se registren a partir de ahora tendrán su perfil de cliente creado automáticamente. No se requiere ninguna acción adicional.

## Verificación

Para verificar que todo funciona correctamente:

1. Ejecuta el comando de corrección (si tienes usuarios existentes)
2. Intenta guardar una dirección en `/customer`
3. El error "Usuario no tiene perfil de cliente" ya no debería aparecer

## Notas Técnicas

- El perfil de cliente se crea con el campo `phone` si está disponible
- La relación entre `User` y `Customer` es uno a uno (`hasOne`)
- El perfil de cliente es necesario para gestionar direcciones, pedidos y otras funcionalidades del cliente
