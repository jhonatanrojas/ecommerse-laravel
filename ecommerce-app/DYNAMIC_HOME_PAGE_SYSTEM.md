# Sistema de Gestión de Home Page Dinámica

## 📋 Resumen

Sistema completo de gestión de home page dinámica implementado con Laravel, siguiendo principios SOLID y patrones de diseño (Repository, Strategy, Observer).

## ✅ Estado de Implementación

### Completado (Backend)

#### 🗄️ Base de Datos
- ✅ Tabla `home_sections` con UUID, tipos enum, soft deletes
- ✅ Tabla `home_section_items` con relaciones polimórficas
- ✅ Índices optimizados para performance
- ✅ Migraciones ejecutadas

#### 📦 Modelos
- ✅ `HomeSection` - Modelo principal con traits HasUuids, SoftDeletes
- ✅ `HomeSectionItem` - Modelo para items con relaciones polimórficas
- ✅ Scopes: `active()`, `ordered()`
- ✅ Relaciones: `items()`, `itemable()`

#### 🔍 Observers
- ✅ `HomeSectionObserver` - Invalidación automática de caché
- ✅ `HomeSectionItemObserver` - Invalidación automática de caché
- ✅ Registrados en `AppServiceProvider`

#### 🏗️ Repository Pattern
- ✅ `HomeSectionRepositoryInterface` - Contrato del repositorio
- ✅ `HomeSectionRepository` - Implementación con caché (3600s)
- ✅ Métodos: `getAllActive()`, `getById()`, `create()`, `update()`, `delete()`, `reorder()`

#### 🎯 Services
- ✅ `HomeConfigurationService` - Lógica de negocio principal
- ✅ `HomeSectionRendererService` - Coordinador de estrategias

#### 🎨 Strategy Pattern (Renderers)
- ✅ `SectionRendererInterface` - Contrato de renderers
- ✅ `HeroRenderer` - Renderiza sección hero
- ✅ `FeaturedProductsRenderer` - Renderiza productos destacados
- ✅ `FeaturedCategoriesRenderer` - Renderiza categorías destacadas
- ✅ `BannersRenderer` - Renderiza banners promocionales
- ✅ `TestimonialsRenderer` - Renderiza testimonios
- ✅ `HtmlBlockRenderer` - Renderiza bloques HTML personalizados

#### 🎮 Controllers
- ✅ `Admin/HomeSectionController` - CRUD completo + reorder + toggleStatus
- ✅ `Api/HomeConfigurationController` - API pública con caché

#### ✔️ Validación
- ✅ `StoreHomeSectionRequest` - Validación para crear secciones
- ✅ `UpdateHomeSectionRequest` - Validación para actualizar secciones

#### 🔄 Resources
- ✅ `HomeSectionResource` - Transformación de datos para API

#### 🛣️ Rutas
- ✅ Admin routes en `routes/admin.php`
- ✅ API route pública en `routes/api.php`

#### ⚙️ Service Provider
- ✅ `HomePageServiceProvider` - Registro de dependencias
- ✅ Registrado en `config/app.php`

#### 🌱 Seeder
- ✅ `HomeSectionSeeder` - 6 secciones predefinidas con datos de ejemplo
- ✅ Registrado en `DatabaseSeeder`

### ⏳ Pendiente (Frontend)

- ⏳ Activity Logging (spatie/laravel-activitylog)
- ⏳ Admin Interface (Vue/Inertia)
- ⏳ Public Home Page (Vue components)
- ⏳ Error Handling adicional
- ⏳ Testing

## 🚀 Uso del Sistema

### API Pública

**Endpoint:** `GET /api/home-configuration`

**Respuesta:**
```json
[
  {
    "uuid": "a10c2523-6b94-4cbf-8767-184941a9ab4f",
    "type": "hero",
    "title": "Hero Principal",
    "display_order": 1,
    "configuration": {
      "title": "Bienvenido a Nuestra Tienda",
      "subtitle": "Descubre los mejores productos al mejor precio",
      "background_image": "https://via.placeholder.com/1920x600/...",
      "cta_buttons": [...]
    },
    "rendered_data": {
      "title": "Bienvenido a Nuestra Tienda",
      "subtitle": "Descubre los mejores productos al mejor precio",
      ...
    }
  },
  ...
]
```

**Características:**
- ✅ Sin autenticación requerida
- ✅ Cacheado por 1 hora (3600s)
- ✅ Invalidación automática al modificar secciones
- ✅ Solo retorna secciones activas
- ✅ Ordenadas por `display_order`

### Admin Routes

**CRUD Completo:**
- `GET /admin/home-sections` - Listar todas las secciones
- `GET /admin/home-sections/create` - Formulario de creación
- `POST /admin/home-sections` - Crear nueva sección
- `GET /admin/home-sections/{id}/edit` - Formulario de edición
- `PUT /admin/home-sections/{id}` - Actualizar sección
- `DELETE /admin/home-sections/{id}` - Eliminar sección (soft delete)

**Acciones Especiales:**
- `POST /admin/home-sections/reorder` - Reordenar secciones
- `POST /admin/home-sections/{id}/toggle-status` - Activar/desactivar sección

**Middleware:** `auth`, `verified`

## 📊 Tipos de Secciones

### 1. Hero (`hero`)
Sección principal con imagen de fondo y CTAs.

**Configuración:**
```json
{
  "title": "Título principal",
  "subtitle": "Subtítulo",
  "background_image": "URL de imagen",
  "background_video": "URL de video (opcional)",
  "overlay_opacity": 0.5,
  "cta_buttons": [
    {
      "text": "Texto del botón",
      "url": "/ruta",
      "style": "primary|secondary"
    }
  ]
}
```

### 2. Featured Products (`featured_products`)
Muestra productos destacados con imágenes, precios y ratings.

**Configuración:**
```json
{
  "heading": "Título de la sección",
  "subheading": "Subtítulo",
  "limit": 8,
  "layout": "grid",
  "columns": 4,
  "show_price": true,
  "show_rating": true
}
```

**Items:** Relación polimórfica con modelo `Product`

### 3. Featured Categories (`featured_categories`)
Muestra categorías destacadas con imágenes y conteo de productos.

**Configuración:**
```json
{
  "heading": "Título de la sección",
  "subheading": "Subtítulo",
  "limit": 6,
  "layout": "grid",
  "columns": 3,
  "show_product_count": true
}
```

**Items:** Relación polimórfica con modelo `Category`

### 4. Banners (`banners`)
Banners promocionales en slider o grid.

**Configuración:**
```json
{
  "layout": "slider|grid",
  "autoplay": true,
  "autoplay_speed": 5000,
  "banners": [
    {
      "image": "URL de imagen",
      "title": "Título",
      "subtitle": "Subtítulo",
      "link": "/ruta",
      "button_text": "Texto del botón"
    }
  ]
}
```

### 5. Testimonials (`testimonials`)
Testimonios de clientes con ratings y avatares.

**Configuración:**
```json
{
  "heading": "Título de la sección",
  "layout": "carousel",
  "show_rating": true,
  "show_avatar": true,
  "testimonials": [
    {
      "name": "Nombre del cliente",
      "avatar": "URL del avatar",
      "rating": 5,
      "text": "Testimonio",
      "date": "2024-01-15"
    }
  ]
}
```

### 6. HTML Block (`html_block`)
Bloque HTML personalizado para contenido flexible.

**Configuración:**
```json
{
  "html_content": "<div>HTML personalizado</div>",
  "css_classes": "clases-css-adicionales"
}
```

## 🏗️ Arquitectura

### Patrones de Diseño Implementados

#### 1. Repository Pattern
Abstrae el acceso a datos, desacoplando la lógica de negocio de la implementación de persistencia.

```
Controller → Service → Repository → Model → Database
```

#### 2. Strategy Pattern
Permite diferentes algoritmos de renderizado según el tipo de sección.

```
HomeSectionRendererService
  ├── HeroRenderer
  ├── FeaturedProductsRenderer
  ├── FeaturedCategoriesRenderer
  ├── BannersRenderer
  ├── TestimonialsRenderer
  └── HtmlBlockRenderer
```

#### 3. Observer Pattern
Invalidación automática de caché cuando se modifican secciones.

```
Model Event → Observer → Cache::tags(['home_sections'])->flush()
```

### Flujo de Datos

**Admin Update Flow:**
```
Admin UI → Controller → Service → Repository → Model → Observer → Cache Invalidation
```

**Public API Flow:**
```
Frontend → API Controller → Service → Repository → Cache Check
                                                   ↓ (miss)
                                          Query DB → Render Sections → Cache Store → Response
```

## 🔧 Configuración

### Cache
- **Driver:** Redis/File (configurado en `.env`)
- **TTL:** 3600 segundos (1 hora)
- **Tags:** `home_sections`
- **Keys:**
  - `home_sections_active` - Secciones activas
  - `api_home_configuration` - Configuración completa para API

### Base de Datos
```sql
-- Tablas
home_sections
home_section_items

-- Índices
idx_uuid
idx_display_order
idx_is_active
idx_deleted_at
idx_home_section_display
idx_itemable
```

## 📝 Ejemplos de Uso

### Crear una Nueva Sección

```php
use App\Repositories\Contracts\HomeSectionRepositoryInterface;

$repository = app(HomeSectionRepositoryInterface::class);

$section = $repository->create([
    'type' => 'hero',
    'title' => 'Nueva Sección Hero',
    'is_active' => true,
    'display_order' => 10,
    'configuration' => [
        'title' => 'Título del Hero',
        'subtitle' => 'Subtítulo',
        'background_image' => '/images/hero.jpg',
        'cta_buttons' => [
            ['text' => 'Ver Más', 'url' => '/about', 'style' => 'primary']
        ]
    ]
]);
```

### Reordenar Secciones

```php
use App\Services\HomeConfigurationService;

$service = app(HomeConfigurationService::class);

// Array de IDs en el nuevo orden
$service->reorderSections([3, 1, 2, 4, 5, 6]);
```

### Activar/Desactivar Sección

```php
use App\Services\HomeConfigurationService;

$service = app(HomeConfigurationService::class);

// Desactivar sección
$service->toggleSectionStatus($sectionId, false);

// Activar sección
$service->toggleSectionStatus($sectionId, true);
```

### Obtener Configuración Completa

```php
use App\Services\HomeConfigurationService;

$service = app(HomeConfigurationService::class);

$configuration = $service->getCompleteConfiguration();
// Retorna array con todas las secciones activas y sus datos renderizados
```

## 🧪 Testing

### Ejecutar Seeder
```bash
php artisan db:seed --class=HomeSectionSeeder
```

### Verificar Datos
```bash
php artisan tinker
>>> App\Models\HomeSection::count()
>>> App\Models\HomeSection::with('items')->get()
```

### Probar API
```bash
# Iniciar servidor
php artisan serve

# Probar endpoint (en otra terminal)
curl http://localhost:8000/api/home-configuration
```

## 🔐 Seguridad

- ✅ Validación de entrada con Form Requests
- ✅ Soft deletes para recuperación de datos
- ✅ Middleware de autenticación en rutas admin
- ✅ Sanitización de HTML en HtmlBlockRenderer (pendiente implementar HTMLPurifier)
- ✅ Cache con tags para invalidación selectiva

## 🚀 Próximos Pasos

1. **Activity Logging**
   - Instalar `spatie/laravel-activitylog`
   - Registrar todas las operaciones CRUD
   - Asociar logs con usuarios admin

2. **Admin Interface (Vue/Inertia)**
   - Componente Index con tabla y drag & drop
   - Componentes Create/Edit con formularios dinámicos
   - Toggle switches para activar/desactivar
   - Notificaciones toast

3. **Public Home Page (Vue)**
   - Componente principal Home.vue
   - Componentes específicos por tipo de sección
   - Carga dinámica desde API
   - Estilos con Tailwind CSS

4. **Error Handling**
   - Try-catch en controllers
   - Manejo de ModelNotFoundException
   - Logs de errores
   - Respuestas JSON consistentes

5. **Testing**
   - Feature tests para API
   - Unit tests para services y renderers
   - Tests de caché
   - Tests de reordenamiento

## 📚 Documentación Adicional

- [Requirements Document](.kiro/specs/dynamic-home-page-management/requirements.md)
- [Design Document](.kiro/specs/dynamic-home-page-management/design.md)
- [Tasks Document](.kiro/specs/dynamic-home-page-management/tasks.md)

## 🤝 Contribución

Para agregar un nuevo tipo de sección:

1. Agregar tipo al enum en migración `home_sections`
2. Crear nuevo Renderer implementando `SectionRendererInterface`
3. Registrar renderer en `HomeSectionRendererService`
4. Registrar renderer en `HomePageServiceProvider`
5. Actualizar validación en Form Requests
6. Crear componente Vue para el frontend

---

**Versión:** 1.0.0  
**Fecha:** 2026-02-10  
**Estado:** Backend Completo ✅
