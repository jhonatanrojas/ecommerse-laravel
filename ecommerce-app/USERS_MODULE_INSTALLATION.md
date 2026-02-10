# Instalación del Módulo de Usuarios

## Pasos de Instalación

### 1. Verificar que Spatie Permission esté instalado

El módulo requiere `spatie/laravel-permission`. Si no está instalado:

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 2. Ejecutar el Seeder de Permisos (Opcional)

Para crear los permisos del módulo de usuarios:

```bash
php artisan db:seed --class=UserModuleSeeder
```

### 3. Verificar las Rutas

Las rutas ya están registradas en `routes/admin.php`. Verifica que estén disponibles:

```bash
php artisan route:list --name=admin.users
```

Deberías ver:

```
GET    /admin/users ..................... admin.users.index
GET    /admin/users/create .............. admin.users.create
POST   /admin/users ..................... admin.users.store
GET    /admin/users/{user}/edit ......... admin.users.edit
PUT    /admin/users/{user} .............. admin.users.update
DELETE /admin/users/{user} .............. admin.users.destroy
PATCH  /admin/users/{user}/toggle-status  admin.users.toggle-status
```

### 4. Verificar el Service Provider

Los bindings ya están registrados en `app/Providers/AppServiceProvider.php`. No requiere acción adicional.

### 5. Limpiar Cache (Recomendado)

```bash
php artisan config:clear
php artisan cache:clear
hpp artisan view:clear
```

### 6. Acceder al Módulo

Inicia sesión como administrador y navega a:

```
http://tu-dominio.com/admin/users
```

## Verificación de Instalación

### Checklist

- [ ] Spatie Permission instalado y migrado
- [ ] Rutas registradas correctamente
- [ ] Service Provider con bindings configurado
- [ ] Enlace en el sidebar del admin visible
- [ ] Permisos creados (si ejecutaste el seeder)
- [ ] Acceso al módulo funcional

## Solución de Problemas

### Error: "Target class does not exist"

Ejecuta:
```bash
php artisan config:clear
composer dump-autoload
```

### Error: "Class UserServiceInterface not found"

Verifica que los bindings estén en `AppServiceProvider`:
```bash
php artisan config:cache
```

### Error de permisos en la base de datos

Ejecuta las migraciones de Spatie:
```bash
php artisan migrate
```

### No aparece el enlace en el sidebar

Limpia la cache de vistas:
```bash
php artisan view:clear
```

## Configuración Adicional

### Middleware de Permisos (Opcional)

Para proteger las rutas con permisos, edita `routes/admin.php`:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    
    // Users CRUD con middleware de permisos
    Route::resource('users', UserController::class)
        ->middleware('permission:users.view');
    
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status')
        ->middleware('permission:users.toggle-status');
});
```

### Personalizar Validaciones

Edita los archivos:
- `app/Http/Requests/StoreUserRequest.php`
- `app/Http/Requests/UpdateUserRequest.php`

### Personalizar Vistas

Las vistas están en:
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/edit.blade.php`

## Próximos Pasos

1. Crear roles básicos si no existen (admin, customer, etc.)
2. Asignar permisos a los roles
3. Probar el CRUD completo
4. Personalizar según necesidades del proyecto

## Soporte

Para más información, consulta:
- [Documentación del Módulo](USERS_MODULE_SOLID.md)
- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission)
- [Flowbite Components](https://flowbite.com/docs/components)
