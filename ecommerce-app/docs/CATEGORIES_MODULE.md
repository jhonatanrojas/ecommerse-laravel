# Módulo de Categorías - Documentación

## Descripción General

Módulo completo de gestión de categorías para el panel administrativo del ecommerce, construido siguiendo los principios SOLID y las mejores prácticas de arquitectura Laravel.

## Arquitectura SOLID Implementada

### 1. Single Responsibility Principle (SRP)
Cada clase tiene una única responsabilidad:

- **CategoryController**: Coordina el flujo HTTP y respuestas
- **CategoryService**: Encapsula la lógica de negocio
- **EloquentCategoryRepository**: Maneja el acceso a datos
- **StoreCategoryRequest / UpdateCategoryRequest**: Validan datos de entrada
- **Category Model**: Representa la entidad y sus atributos

### 2. Open/Closed Principle (OCP)
El módulo está abierto a extensiones sin modificar código existente:

- Interfaces permiten agregar nuevas implementaciones
- Scopes en el modelo facilitan agregar filtros
- Service puede extenderse con nuevos métodos sin afectar existentes

### 3. Liskov Substitution Principle (LSP)
Las interfaces permiten intercambiar implementaciones:

```php
// Se puede cambiar EloquentCategoryRepository por otra implementación
// sin romper CategoryService
CategoryRepositoryInterface -> EloquentCategoryRepository
CategoryServiceInterface -> CategoryService
```

### 4. Interface Segregation Principle (ISP)
Interfaces específicas y enfocadas:

- `CategoryRepositoryInterface`: Operaciones de datos
- `CategoryServiceInterface`: Lógica de negocio

### 5. Dependency Inversion Principle (DIP)
El controlador depende de abstracciones, no de implementaciones concretas:

```php
public function __construct(
    protected CategoryServiceInterface $categoryService
) {}
```

## Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── CategoryController.php
│   └── Requests/
│       ├── StoreCategoryRequest.php
│       └── UpdateCategoryRequest.php
├── Models/
│   └── Category.php
├── Repositories/
│   ├── Contracts/
│   │   └── CategoryRepositoryInterface.php
│   └── EloquentCategoryRepository.php
├── Services/
│   ├── Contracts/
│   │   └── CategoryServiceInterface.php
│   └── CategoryService.php
└── Providers/
    └── RepositoryServiceProvider.php

database/
├── factories/
│   └── CategoryFactory.php
├── migrations/
│   └── 2026_02_10_030000_create_categories_table.php
└── seeders/
    └── CategorySeeder.php

resources/views/
└── admin/
    └── categories/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── _form.blade.php

routes/
└── admin.php
```

## Características Implementadas

### Modelo Category
- ✅ Campos: nombre, slug, descripcion, estado
- ✅ Soft deletes
- ✅ Generación automática de slug
- ✅ Scopes: active, search
- ✅ Accessor para estado legible
- ✅ Timestamps y casts

### Repositorio
- ✅ Métodos CRUD completos
- ✅ Paginación con búsqueda
- ✅ Filtrado por estado
- ✅ Búsqueda por slug

### Servicio
- ✅ Lógica de negocio encapsulada
- ✅ Transacciones de base de datos
- ✅ Logging de operaciones
- ✅ Manejo de errores
- ✅ Toggle de estado

### Controlador
- ✅ RESTful resource controller
- ✅ Inyección de dependencias
- ✅ Validación mediante Form Requests
- ✅ Mensajes flash de éxito/error
- ✅ Manejo de excepciones

### Vistas (Flowbite + TailwindCSS)
- ✅ Layout admin responsive
- ✅ Tabla con paginación
- ✅ Búsqueda en tiempo real
- ✅ Formulario reutilizable
- ✅ Toggle de estado inline
- ✅ Confirmación de eliminación
- ✅ Breadcrumbs
- ✅ Alertas de feedback
- ✅ Dark mode support
- ✅ Mobile responsive

## Rutas Disponibles

```php
GET    /admin/categories              -> index
GET    /admin/categories/create       -> create
POST   /admin/categories              -> store
GET    /admin/categories/{id}/edit    -> edit
PUT    /admin/categories/{id}         -> update
DELETE /admin/categories/{id}         -> destroy
PATCH  /admin/categories/{id}/toggle-status -> toggleStatus
```

## Instalación y Configuración

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

### 2. Ejecutar Seeders (Opcional)
```bash
php artisan db:seed --class=CategorySeeder
```

### 3. Verificar Service Provider
El `RepositoryServiceProvider` debe estar registrado en `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\RepositoryServiceProvider::class,
],
```

### 4. Acceder al Módulo
Navega a: `http://tu-dominio.test/admin/categories`

## Uso del Módulo

### Crear una Categoría
1. Click en "Nueva Categoría"
2. Completar formulario (nombre es obligatorio)
3. El slug se genera automáticamente
4. Guardar

### Editar una Categoría
1. Click en el ícono de editar
2. Modificar campos necesarios
3. Actualizar

### Cambiar Estado
- Click directo en el badge de estado (Activo/Inactivo)

### Eliminar una Categoría
- Click en el ícono de eliminar
- Confirmar acción

### Buscar Categorías
- Usar el campo de búsqueda en la parte superior
- Busca en nombre y descripción

## Validaciones

### Store (Crear)
- `nombre`: requerido, string, máx 255 caracteres
- `slug`: opcional, string, único, máx 255 caracteres
- `descripcion`: opcional, string, máx 1000 caracteres
- `estado`: opcional, boolean

### Update (Actualizar)
- Mismas validaciones que Store
- El slug único ignora el registro actual

## Extensibilidad Futura

El módulo está preparado para:

- ✅ Agregar subcategorías (parent_id)
- ✅ Agregar iconos/imágenes
- ✅ Agregar ordenamiento personalizado
- ✅ Agregar filtros avanzados
- ✅ Agregar relaciones con productos
- ✅ Agregar SEO metadata
- ✅ Implementar caché
- ✅ Agregar API endpoints

## Testing

### Crear Tests Unitarios
```php
// tests/Unit/CategoryServiceTest.php
public function test_can_create_category()
{
    $service = app(CategoryServiceInterface::class);
    $category = $service->createCategory([
        'nombre' => 'Test Category',
        'estado' => true,
    ]);
    
    $this->assertInstanceOf(Category::class, $category);
    $this->assertEquals('Test Category', $category->nombre);
}
```

### Crear Tests de Feature
```php
// tests/Feature/CategoryControllerTest.php
public function test_can_view_categories_index()
{
    $response = $this->actingAs($user)
        ->get(route('admin.categories.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.categories.index');
}
```

## Mejores Prácticas Aplicadas

1. ✅ **Inyección de Dependencias**: Todas las dependencias se inyectan
2. ✅ **Type Hinting**: Todos los métodos tienen tipos definidos
3. ✅ **Form Requests**: Validación separada del controlador
4. ✅ **Transacciones**: Operaciones críticas en transacciones
5. ✅ **Logging**: Registro de operaciones importantes
6. ✅ **Soft Deletes**: Eliminación lógica de registros
7. ✅ **Scopes**: Queries reutilizables en el modelo
8. ✅ **Factory & Seeder**: Datos de prueba fáciles de generar
9. ✅ **Componentes Reutilizables**: Formulario compartido
10. ✅ **Responsive Design**: Mobile-first con Tailwind

## Soporte

Para dudas o problemas con el módulo, revisar:
- Logs de Laravel: `storage/logs/laravel.log`
- Validaciones en Form Requests
- Service Provider registrado correctamente
- Migraciones ejecutadas

## Licencia

Este módulo es parte del proyecto ecommerce y sigue la misma licencia del proyecto principal.
