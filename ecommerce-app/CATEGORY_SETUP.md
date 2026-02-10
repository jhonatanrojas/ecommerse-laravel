# 🚀 Setup Rápido - Módulo de Categorías

## Pasos de Instalación

### 1. Verificar Migración Existente
La migración de categorías ya existe en:
```
database/migrations/2026_02_10_011130_create_categories_table.php
```

Si aún no has ejecutado las migraciones:
```bash
php artisan migrate
```

### 2. Poblar Base de Datos (Opcional)
```bash
php artisan db:seed --class=CategorySeeder
```

### 3. Verificar Rutas
```bash
php artisan route:list --name=admin.categories
```

### 4. Ejecutar Tests
```bash
# Tests unitarios
php artisan test --filter=CategoryServiceTest

# Tests de feature
php artisan test --filter=CategoryControllerTest

# Todos los tests
php artisan test
```

### 5. Acceder al Módulo
```
http://tu-dominio.test/admin/categories
```

## Estructura de la Base de Datos

La tabla `categories` incluye:
- `id`: Primary key auto-incremental
- `uuid`: Identificador público único
- `parent_id`: Para jerarquía de categorías (nullable)
- `name`: Nombre de la categoría
- `slug`: URL amigable (único)
- `description`: Descripción (nullable)
- `image`: Ruta de imagen (nullable)
- `order`: Orden de visualización
- `is_active`: Estado activo/inactivo
- `created_by`, `updated_by`, `deleted_by`: Auditoría
- `timestamps` y `softDeletes`

## Verificación Rápida

### Verificar Service Provider
El archivo `config/app.php` debe incluir:
```php
App\Providers\RepositoryServiceProvider::class,
```

### Verificar Rutas
El archivo `routes/web.php` debe incluir:
```php
require __DIR__.'/admin.php';
```

## Estructura Creada

```
✅ Modelo: app/Models/Category.php (actualizado con campos reales)
✅ Interfaces:
   - app/Repositories/Contracts/CategoryRepositoryInterface.php
   - app/Services/Contracts/CategoryServiceInterface.php
✅ Repositorio: app/Repositories/EloquentCategoryRepository.php
✅ Servicio: app/Services/CategoryService.php (con auditoría)
✅ Controlador: app/Http/Controllers/Admin/CategoryController.php
✅ Requests:
   - app/Http/Requests/StoreCategoryRequest.php
   - app/Http/Requests/UpdateCategoryRequest.php
✅ Vistas:
   - resources/views/layouts/admin.blade.php
   - resources/views/admin/categories/index.blade.php
   - resources/views/admin/categories/create.blade.php
   - resources/views/admin/categories/edit.blade.php
   - resources/views/admin/categories/_form.blade.php
✅ Factory: database/factories/CategoryFactory.php
✅ Seeder: database/seeders/CategorySeeder.php
✅ Rutas: routes/admin.php
✅ Tests:
   - tests/Unit/CategoryServiceTest.php
   - tests/Feature/CategoryControllerTest.php
✅ Documentación: docs/CATEGORIES_MODULE.md
```

## Funcionalidades Disponibles

- ✅ Listar categorías con paginación
- ✅ Buscar categorías por nombre/descripción
- ✅ Crear nueva categoría
- ✅ Editar categoría existente
- ✅ Eliminar categoría (soft delete)
- ✅ Cambiar estado (activo/inactivo)
- ✅ Generación automática de slug y UUID
- ✅ Validación de formularios
- ✅ Mensajes de feedback
- ✅ Diseño responsive
- ✅ Dark mode
- ✅ Auditoría completa (created_by, updated_by, deleted_by)
- ✅ Soporte para jerarquía (parent_id)
- ✅ Ordenamiento personalizado

## Principios SOLID Aplicados

✅ **S** - Single Responsibility: Cada clase tiene una única responsabilidad
✅ **O** - Open/Closed: Abierto a extensión, cerrado a modificación
✅ **L** - Liskov Substitution: Interfaces intercambiables
✅ **I** - Interface Segregation: Interfaces específicas
✅ **D** - Dependency Inversion: Dependencias de abstracciones

## Comandos Útiles

```bash
# Crear una categoría desde tinker
php artisan tinker
>>> Category::create(['name' => 'Test', 'is_active' => true]);

# Ver todas las categorías
>>> Category::all();

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Compilar assets
npm run dev
# o
npm run build
```

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: Route not found
```bash
php artisan route:clear
php artisan route:cache
```

### Error: View not found
```bash
php artisan view:clear
```

### Error: SQLSTATE
Verificar que la migración se ejecutó correctamente:
```bash
php artisan migrate:status
```

### Error: Column not found
Si la migración ya existía con campos diferentes, verifica que todos los campos del modelo coincidan con la base de datos.

## Próximos Pasos

1. Personalizar el layout admin según tu diseño
2. Agregar permisos con Spatie Laravel Permission
3. Implementar relaciones con productos
4. Agregar upload de imágenes
5. Implementar subcategorías (ya soportado en BD)
6. Agregar filtros avanzados
7. Implementar API REST

## Soporte

Para más detalles, consulta: `docs/CATEGORIES_MODULE.md`
