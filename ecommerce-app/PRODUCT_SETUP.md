# 🚀 Setup Rápido - Módulo de Productos

## Pasos de Instalación

### 1. Verificar Migraciones Existentes
Las migraciones de productos ya existen:
```
database/migrations/2026_02_10_011253_create_products_table.php
database/migrations/2026_02_10_011334_create_product_images_table.php
```

Si aún no has ejecutado las migraciones:
```bash
php artisan migrate
```

### 2. Crear el enlace simbólico para Storage
```bash
php artisan storage:link
```

### 3. Poblar Base de Datos (Opcional)
```bash
# Primero asegúrate de tener categorías
php artisan db:seed --class=CategorySeeder

# Luego crea productos
php artisan db:seed --class=ProductSeeder
```

### 4. Verificar Rutas
```bash
php artisan route:list --name=admin.products
```

### 5. Acceder al Módulo
```
http://tu-dominio.test/admin/products
```

## Estructura de la Base de Datos

### Tabla `products`
- `id`: Primary key auto-incremental
- `uuid`: Identificador público único
- `category_id`: Relación con categorías (nullable)
- `name`: Nombre del producto
- `slug`: URL amigable (único)
- `sku`: Código único del producto
- `description`: Descripción completa
- `short_description`: Descripción breve (500 caracteres)
- `price`: Precio de venta
- `compare_price`: Precio antes del descuento (nullable)
- `cost`: Costo del producto (nullable)
- `stock`: Cantidad en inventario
- `low_stock_threshold`: Umbral de alerta de stock bajo
- `weight`: Peso en gramos (nullable)
- `dimensions`: JSON con largo, ancho, alto (nullable)
- `is_active`: Estado activo/inactivo
- `is_featured`: Producto destacado
- `meta_title`, `meta_description`, `meta_keywords`: SEO
- `created_by`, `updated_by`, `deleted_by`: Auditoría
- `timestamps` y `softDeletes`

### Tabla `product_images`
- `id`: Primary key auto-incremental
- `uuid`: Identificador público único
- `product_id`: Relación con productos
- `image_path`: Ruta de la imagen
- `thumbnail_path`: Ruta del thumbnail (nullable)
- `alt_text`: Texto alternativo (nullable)
- `is_primary`: Imagen principal
- `order`: Orden de visualización
- `timestamps` y `softDeletes`

## Estructura Creada

```
✅ Modelo: app/Models/Product.php (actualizado)
✅ Modelo: app/Models/ProductImage.php (actualizado)
✅ Interfaces:
   - app/Repositories/Contracts/ProductRepositoryInterface.php
   - app/Services/Contracts/ProductServiceInterface.php
✅ Repositorio: app/Repositories/EloquentProductRepository.php
✅ Servicio: app/Services/ProductService.php (con manejo de imágenes)
✅ Controlador: app/Http/Controllers/Admin/ProductController.php
✅ Requests:
   - app/Http/Requests/StoreProductRequest.php
   - app/Http/Requests/UpdateProductRequest.php
✅ Vistas:
   - resources/views/admin/products/index.blade.php
   - resources/views/admin/products/create.blade.php
   - resources/views/admin/products/edit.blade.php
   - resources/views/admin/products/_form.blade.php
✅ Factory: database/factories/ProductFactory.php
✅ Seeder: database/seeders/ProductSeeder.php
✅ Rutas: routes/admin.php (actualizado)
✅ Service Provider: app/Providers/RepositoryServiceProvider.php (actualizado)
✅ Layout Admin: resources/views/layouts/admin.blade.php (menú actualizado)
```

## Funcionalidades Disponibles

- ✅ Listar productos con paginación
- ✅ Buscar productos por nombre, SKU o descripción
- ✅ Filtrar por categoría
- ✅ Crear nuevo producto
- ✅ Editar producto existente
- ✅ Eliminar producto (soft delete)
- ✅ Cambiar estado (activo/inactivo)
- ✅ Marcar como destacado
- ✅ Subir múltiples imágenes (máx 5, 2MB cada una)
- ✅ Eliminar imágenes individuales
- ✅ Imagen principal automática
- ✅ Generación automática de slug y UUID
- ✅ Validación completa de formularios
- ✅ Alertas de stock bajo
- ✅ Indicadores visuales de stock
- ✅ Precios con descuento
- ✅ Auditoría completa
- ✅ Diseño responsive
- ✅ Dark mode

## Principios SOLID Aplicados

✅ **S** - Single Responsibility: Cada clase tiene una única responsabilidad
✅ **O** - Open/Closed: Abierto a extensión, cerrado a modificación
✅ **L** - Liskov Substitution: Interfaces intercambiables
✅ **I** - Interface Segregation: Interfaces específicas
✅ **D** - Dependency Inversion: Dependencias de abstracciones

## Comandos Útiles

```bash
# Crear un producto desde tinker
php artisan tinker
>>> Product::create([
    'name' => 'Test Product',
    'sku' => 'TEST-001',
    'price' => 99.99,
    'stock' => 10,
    'is_active' => true
]);

# Ver todos los productos
>>> Product::with('category', 'images')->get();

# Productos con bajo stock
>>> Product::whereColumn('stock', '<=', 'low_stock_threshold')->get();

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Compilar assets
npm run dev
# o
npm run build
```

## Manejo de Imágenes

### Configuración de Storage
Las imágenes se guardan en `storage/app/public/products/`

### Formatos Soportados
- JPEG, JPG, PNG, WEBP
- Máximo 2MB por imagen
- Máximo 5 imágenes por producto

### Acceso a Imágenes
```php
// En Blade
<img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">

// Usando accessor
<img src="{{ $image->url }}" alt="{{ $product->name }}">
```

## Troubleshooting

### Error: Storage link not found
```bash
php artisan storage:link
```

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

### Error: SQLSTATE (UUID)
Verifica que los modelos Product y ProductImage NO usen el trait `HasUuids` de Laravel. El UUID debe ser solo un campo adicional, no la clave primaria.

### Error: File upload failed
Verifica permisos en `storage/app/public/`:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Validaciones Importantes

### Store (Crear)
- `name`: requerido, string, máx 255
- `sku`: requerido, único, máx 255
- `price`: requerido, numérico, mín 0
- `compare_price`: opcional, debe ser mayor que price
- `stock`: requerido, entero, mín 0
- `category_id`: opcional, debe existir
- `images`: máx 5, cada una máx 2MB

### Update (Actualizar)
- Mismas validaciones que Store
- SKU único ignora el registro actual

## Próximos Pasos

1. Implementar variantes de productos
2. Agregar sistema de reviews
3. Implementar descuentos y cupones
4. Agregar historial de precios
5. Implementar búsqueda avanzada
6. Agregar exportación a CSV/Excel
7. Implementar API REST
8. Agregar notificaciones de stock bajo
9. Implementar sistema de etiquetas/tags
10. Agregar galería de imágenes mejorada

## Soporte

Para más detalles sobre la arquitectura SOLID implementada, consulta: `docs/CATEGORIES_MODULE.md` (aplica los mismos principios)
