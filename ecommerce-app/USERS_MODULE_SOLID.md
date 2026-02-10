# Módulo de Usuarios - Arquitectura SOLID

## Descripción General

Módulo completo de gestión de usuarios para el panel administrativo del ecommerce, construido aplicando estrictamente los principios SOLID y patrones de diseño profesionales.

## Arquitectura del Módulo

### Principios SOLID Aplicados

#### 1. Single Responsibility Principle (SRP)
- **UserController**: Solo maneja las peticiones HTTP y delega la lógica de negocio al servicio
- **UserService**: Contiene toda la lógica de negocio relacionada con usuarios
- **UserRepository**: Responsable únicamente de las operaciones de base de datos
- **Request Classes**: Cada clase de validación tiene una única responsabilidad (crear o actualizar)

#### 2. Open/Closed Principle (OCP)
- Las interfaces permiten extender funcionalidad sin modificar código existente
- Nuevas implementaciones de repositorio o servicio pueden agregarse sin cambiar el controlador

#### 3. Liskov Substitution Principle (LSP)
- Cualquier implementación de `UserRepositoryInterface` puede sustituir a `UserRepository`
- Cualquier implementación de `UserServiceInterface` puede sustituir a `UserService`

#### 4. Interface Segregation Principle (ISP)
- Interfaces específicas y cohesivas
- No se fuerza a implementar métodos innecesarios

#### 5. Dependency Inversion Principle (DIP)
- El controlador depende de abstracciones (interfaces), no de implementaciones concretas
- Inyección de dependencias mediante el contenedor de Laravel

## Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── UserController.php          # Controlador principal
│   └── Requests/
│       ├── StoreUserRequest.php            # Validación para crear
│       └── UpdateUserRequest.php           # Validación para actualizar
├── Services/
│   ├── Contracts/
│   │   └── UserServiceInterface.php        # Contrato del servicio
│   └── UserService.php                     # Lógica de negocio
└── Repositories/
    ├── Contracts/
    │   └── UserRepositoryInterface.php     # Contrato del repositorio
    └── UserRepository.php                  # Acceso a datos

resources/views/admin/users/
├── index.blade.php                         # Listado con filtros
├── create.blade.php                        # Formulario de creación
└── edit.blade.php                          # Formulario de edición

database/seeders/
└── UserModuleSeeder.php                    # Seeder de permisos
```

## Funcionalidades Implementadas

### CRUD Completo

#### 1. Index (Listado)
- **Ruta**: `GET /admin/users`
- **Filtros disponibles**:
  - Búsqueda por nombre o email
  - Filtro por rol
  - Filtro por estado (activo/inactivo)
  - Filtro por rango de fechas
- **Características**:
  - Paginación
  - Badges de roles con colores
  - Toggle de estado inline
  - Diseño responsive con Flowbite

#### 2. Create (Crear)
- **Ruta**: `GET /admin/users/create`
- **Campos**:
  - Nombre completo (requerido)
  - Email (requerido, único)
  - Teléfono (opcional)
  - Contraseña (requerida, con confirmación)
  - Estado (activo/inactivo)
  - Roles (requerido, múltiple selección)
  - Permisos individuales (opcional)
- **Validaciones**:
  - Email único en la base de datos
  - Contraseña con requisitos de seguridad
  - Al menos un rol obligatorio

#### 3. Edit (Editar)
- **Ruta**: `GET /admin/users/{id}/edit`
- **Características**:
  - Contraseña opcional (solo si se desea cambiar)
  - Sincronización de roles y permisos
  - Validación de email único excluyendo el usuario actual
  - Protección: no se puede editar el propio usuario

#### 4. Update (Actualizar)
- **Ruta**: `PUT /admin/users/{id}`
- **Transacciones**:
  - Uso de transacciones de base de datos
  - Rollback automático en caso de error
  - Logs de errores

#### 5. Delete (Eliminar)
- **Ruta**: `DELETE /admin/users/{id}`
- **Protecciones**:
  - No se puede eliminar el usuario autenticado
  - Soft delete para recuperación
  - Confirmación mediante JavaScript

#### 6. Toggle Status (Cambiar Estado)
- **Ruta**: `PATCH /admin/users/{id}/toggle-status`
- **Características**:
  - Cambio rápido de estado activo/inactivo
  - No se puede desactivar el usuario autenticado
  - Actualización inline sin recargar página

## Integración con Spatie Permission

### Roles y Permisos

El módulo está completamente integrado con Spatie Laravel Permission:

- **Asignación de roles**: Múltiples roles por usuario
- **Permisos individuales**: Permisos adicionales más allá de los roles
- **Sincronización**: Uso de `syncRoles()` y `syncPermissions()`

### Permisos del Módulo

```php
'users.view'          // Ver listado de usuarios
'users.create'        // Crear usuarios
'users.edit'          // Editar usuarios
'users.delete'        // Eliminar usuarios
'users.toggle-status' // Cambiar estado de usuarios
```

## Validaciones

### StoreUserRequest (Crear)
- Nombre: requerido, string, máximo 255 caracteres
- Email: requerido, email válido, único
- Contraseña: requerida, confirmación, requisitos de seguridad
- Teléfono: opcional, string, máximo 20 caracteres
- Roles: requerido, array, mínimo 1, deben existir en BD
- Permisos: opcional, array, deben existir en BD

### UpdateUserRequest (Actualizar)
- Nombre: requerido, string, máximo 255 caracteres
- Email: requerido, email válido, único (excepto el usuario actual)
- Contraseña: opcional, confirmación, requisitos de seguridad
- Teléfono: opcional, string, máximo 20 caracteres
- Roles: requerido, array, mínimo 1, deben existir en BD
- Permisos: opcional, array, deben existir en BD

## Diseño UI con Flowbite

### Componentes Utilizados

1. **Tablas**: Diseño responsive con hover effects
2. **Formularios**: Inputs, selects y checkboxes estilizados
3. **Badges**: Para roles y estados con colores semánticos
4. **Botones**: Acciones con iconos SVG
5. **Filtros**: Grid responsive con múltiples opciones
6. **Alertas**: Mensajes de éxito y error
7. **Breadcrumbs**: Navegación contextual

### Características de Diseño

- **Responsive**: Adaptado a móviles, tablets y desktop
- **Dark Mode**: Soporte completo para modo oscuro
- **Accesibilidad**: Labels, ARIA attributes y navegación por teclado
- **Iconografía**: SVG icons consistentes con Flowbite

## Uso del Módulo

### 1. Registrar Bindings

Los bindings ya están registrados en `AppServiceProvider`:

```php
$this->app->bind(
    \App\Repositories\Contracts\UserRepositoryInterface::class,
    \App\Repositories\UserRepository::class
);

$this->app->bind(
    \App\Services\Contracts\UserServiceInterface::class,
    \App\Services\UserService::class
);
```

### 2. Ejecutar Seeder (Opcional)

Para crear los permisos del módulo:

```bash
php artisan db:seed --class=UserModuleSeeder
```

### 3. Acceder al Módulo

Navegar a: `http://tu-dominio.com/admin/users`

## Rutas Disponibles

```php
GET    /admin/users                    # Listado
GET    /admin/users/create             # Formulario crear
POST   /admin/users                    # Guardar nuevo
GET    /admin/users/{id}/edit          # Formulario editar
PUT    /admin/users/{id}               # Actualizar
DELETE /admin/users/{id}               # Eliminar
PATCH  /admin/users/{id}/toggle-status # Cambiar estado
```

## Seguridad

### Protecciones Implementadas

1. **Validación de entrada**: Request classes con reglas estrictas
2. **Protección CSRF**: Tokens en todos los formularios
3. **Hashing de contraseñas**: Automático mediante Laravel
4. **Soft Deletes**: Recuperación de usuarios eliminados
5. **Transacciones**: Integridad de datos garantizada
6. **Logs de errores**: Registro de excepciones
7. **Protección de auto-modificación**: No se puede eliminar/desactivar a sí mismo

## Extensibilidad

### Agregar Nuevas Funcionalidades

1. **Nuevo método en el repositorio**:
   - Agregar método en `UserRepositoryInterface`
   - Implementar en `UserRepository`

2. **Nueva lógica de negocio**:
   - Agregar método en `UserServiceInterface`
   - Implementar en `UserService`
   - Usar el repositorio inyectado

3. **Nueva ruta/acción**:
   - Agregar método en `UserController`
   - Registrar ruta en `routes/admin.php`
   - Crear vista si es necesario

## Testing (Recomendaciones)

### Unit Tests
- Testear métodos del repositorio
- Testear lógica de negocio del servicio
- Mockear dependencias

### Feature Tests
- Testear rutas y respuestas HTTP
- Testear validaciones de formularios
- Testear permisos y autorizaciones

### Ejemplo de Test

```php
public function test_admin_can_create_user()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $response = $this->actingAs($admin)
        ->post('/admin/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [1],
        ]);
    
    $response->assertRedirect('/admin/users');
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
}
```

## Mantenimiento

### Logs
Los errores se registran automáticamente en `storage/logs/laravel.log`

### Cache
Spatie Permission cachea roles y permisos automáticamente. Se limpia al actualizar.

### Migraciones
No se requieren migraciones adicionales. El módulo usa las tablas existentes de Spatie Permission.

## Conclusión

Este módulo implementa las mejores prácticas de desarrollo Laravel:
- ✅ Arquitectura SOLID
- ✅ Separación de responsabilidades
- ✅ Inyección de dependencias
- ✅ Validaciones robustas
- ✅ Diseño profesional con Flowbite
- ✅ Integración completa con Spatie Permission
- ✅ Código mantenible y escalable
