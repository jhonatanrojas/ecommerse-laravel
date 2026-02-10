# Módulo de Ajustes Generales de la Tienda - Instalación

## 📋 Descripción

Módulo completo para gestionar los ajustes globales de la tienda desde el panel administrativo, construido siguiendo los principios SOLID.

## 🏗️ Arquitectura SOLID

### Principios Aplicados

#### 1. **Single Responsibility Principle (SRP)**
- **Repositorio**: `EloquentStoreSettingRepository` - Solo maneja acceso a datos
- **Servicio**: `StoreSettingService` - Solo maneja lógica de negocio
- **Servicio de Archivos**: `FileService` - Solo maneja operaciones de archivos
- **Controlador**: `StoreSettingController` - Solo maneja peticiones HTTP
- **Request**: `UpdateStoreSettingRequest` - Solo valida datos de entrada

#### 2. **Open/Closed Principle (OCP)**
- Los servicios pueden extenderse sin modificar el código existente
- Ejemplo: `FileService` puede soportar diferentes drivers (S3, local, etc.)

#### 3. **Liskov Substitution Principle (LSP)**
- Todas las implementaciones respetan sus contratos de interfaz
- Puedes cambiar `EloquentStoreSettingRepository` por otra implementación sin romper el código

#### 4. **Interface Segregation Principle (ISP)**
- Interfaces específicas y enfocadas:
  - `StoreSettingRepositoryInterface` - Solo métodos de repositorio
  - `StoreSettingServiceInterface` - Solo métodos de servicio
  - `FileServiceInterface` - Solo métodos de archivos

#### 5. **Dependency Inversion Principle (DIP)**
- El controlador depende de `StoreSettingServiceInterface`, no de la implementación concreta
- El servicio depende de `StoreSettingRepositoryInterface` y `FileServiceInterface`
- Todas las dependencias se inyectan a través del constructor

## 📦 Archivos Creados

### Migración
```
database/migrations/2026_02_10_050336_create_store_settings_table.php
```

### Modelo
```
app/Models/StoreSetting.php
```

### Repositorios
```
app/Repositories/Contracts/StoreSettingRepositoryInterface.php
app/Repositories/Eloquent/EloquentStoreSettingRepository.php
```

### Servicios
```
app/Services/Contracts/StoreSettingServiceInterface.php
app/Services/StoreSettingService.php
app/Services/Contracts/FileServiceInterface.php
app/Services/FileService.php
```

### Controlador
```
app/Http/Controllers/Admin/StoreSettingController.php
```

### Request
```
app/Http/Requests/UpdateStoreSettingRequest.php
```

### Vistas
```
resources/views/admin/settings/store/edit.blade.php
```

### Rutas
```
routes/admin.php (actualizado)
```

### Providers
```
app/Providers/RepositoryServiceProvider.php (actualizado)
```

### Layout
```
resources/views/layouts/admin.blade.php (actualizado con enlace a Ajustes)
```

## 🚀 Instalación

### 1. Ejecutar la migración

```bash
php artisan migrate
```

Esto creará la tabla `store_settings` con un registro por defecto.

### 2. Crear el enlace simbólico para storage (si no existe)

```bash
php artisan storage:link
```

Esto permite que las imágenes subidas sean accesibles públicamente.

### 3. Verificar permisos de storage

Asegúrate de que el directorio `storage/app/public` tenga permisos de escritura:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🎯 Uso

### Acceder al módulo

1. Inicia sesión en el panel administrativo
2. En el sidebar, haz clic en **"Ajustes"**
3. Configura los ajustes de tu tienda:
   - Nombre de la tienda
   - Logo (imagen)
   - Moneda principal
   - Símbolo de moneda
   - Tasa de impuesto
   - Email de soporte
   - Email transaccional
   - Modo de mantenimiento

### Rutas disponibles

- **GET** `/admin/settings/store` - Formulario de edición
- **PUT** `/admin/settings/store` - Actualizar ajustes

## 🔧 Funcionalidades

### Gestión de Logo
- Subida de imágenes (JPEG, PNG, GIF, SVG)
- Tamaño máximo: 2MB
- Eliminación automática del logo anterior al subir uno nuevo
- Vista previa del logo actual

### Validaciones
- Nombre de tienda: requerido, máximo 255 caracteres
- Logo: opcional, debe ser imagen válida
- Moneda: requerida, máximo 10 caracteres
- Símbolo de moneda: requerido, máximo 5 caracteres
- Tasa de impuesto: requerida, numérica, entre 0 y 100
- Emails: opcionales, deben ser válidos
- Modo de mantenimiento: checkbox booleano

### Características SOLID

#### Desacoplamiento
```php
// El controlador no conoce la implementación específica
public function __construct(
    private StoreSettingServiceInterface $storeSettingService
) {}
```

#### Inyección de Dependencias
```php
// El servicio recibe sus dependencias por constructor
public function __construct(
    private StoreSettingRepositoryInterface $repository,
    private FileServiceInterface $fileService
) {}
```

#### Responsabilidad Única
- **FileService**: Solo maneja archivos
- **StoreSettingService**: Solo maneja lógica de negocio
- **EloquentStoreSettingRepository**: Solo maneja acceso a datos

## 🧪 Testing (Opcional)

Para crear tests del módulo:

```bash
php artisan make:test StoreSettingControllerTest
php artisan make:test StoreSettingServiceTest
```

## 📝 Notas Adicionales

### Patrón Singleton
La tabla `store_settings` está diseñada para tener un solo registro (configuración global). El repositorio siempre obtiene el primer registro.

### Extensibilidad
Si necesitas agregar más ajustes:

1. Agrega el campo en la migración
2. Agrégalo al `$fillable` del modelo
3. Agrega el input en la vista
4. Agrega la validación en el Request

### Seguridad
- Todas las rutas están protegidas con middleware `auth` y `verified`
- Las validaciones previenen inyección de datos maliciosos
- Los archivos subidos se validan por tipo y tamaño

## 🎨 Integración con Flowbite

El formulario utiliza componentes de Flowbite Admin Dashboard:
- Inputs con estilos dark mode
- File upload con drag & drop visual
- Toggles para checkboxes
- Botones con estados hover y focus
- Alertas de éxito/error

## 🔄 Flujo de Datos

```
Usuario → Controlador → Servicio → Repositorio → Base de Datos
                ↓
         FileService (si hay logo)
```

## ✅ Checklist de Verificación

- [x] Migración creada
- [x] Modelo con casts y accessors
- [x] Interfaces de repositorio y servicios
- [x] Implementaciones concretas
- [x] Controlador con inyección de dependencias
- [x] Request de validación
- [x] Rutas registradas
- [x] Vista Blade con Flowbite
- [x] Sidebar actualizado
- [x] Service Provider actualizado
- [x] Principios SOLID aplicados

## 🎉 ¡Listo!

El módulo está completamente funcional y listo para usar. Solo necesitas ejecutar la migración y configurar tu base de datos.
