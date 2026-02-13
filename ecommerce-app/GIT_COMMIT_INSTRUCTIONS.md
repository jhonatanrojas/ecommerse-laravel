# Instrucciones para Agregar Cambios al Git

## Scripts Disponibles

Se han creado scripts automatizados para agregar todos los archivos del módulo de Shipping Status al repositorio git con commits organizados.

### Para Windows

```cmd
git-add-shipping-status-module.bat
```

### Para Linux/Mac

```bash
chmod +x git-add-shipping-status-module.sh
./git-add-shipping-status-module.sh
```

## Commits que se Crearán

Los scripts crearán 7 commits organizados:

### 1. Modelo y Migración
```
feat: add ShippingStatus model and migration

- Create ShippingStatus model with relationships and scopes
- Add migration to create shipping_statuses table
- Add shipping_status_id column to orders table
- Include 7 predefined shipping statuses
- Add ShippingStatusFactory for testing
```

**Archivos incluidos:**
- `app/Models/ShippingStatus.php`
- `database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php`
- `database/factories/ShippingStatusFactory.php`

### 2. Controladores y Validación
```
feat: add shipping status controllers and validation

- Create AdminShippingStatusController for CRUD operations
- Create AdminOrderShippingStatusUpdateController for order updates
- Add request validation classes (Store, Update, UpdateOrder)
- Add ShippingStatusResource for API responses
```

**Archivos incluidos:**
- `app/Http/Controllers/Admin/AdminShippingStatusController.php`
- `app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php`
- `app/Http/Requests/Admin/StoreShippingStatusRequest.php`
- `app/Http/Requests/Admin/UpdateShippingStatusRequest.php`
- `app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php`
- `app/Http/Resources/ShippingStatusResource.php`

### 3. Integración con Order
```
feat: integrate ShippingStatus with Order model

- Add shippingStatus() relationship to Order model
- Add setShippingStatus() helper method
- Update OrderResource to include shipping_status
- Update EloquentOrderRepository with eager loading
- Add shipping_status_id to fillable fields
```

**Archivos incluidos:**
- `app/Models/Order.php`
- `app/Http/Resources/OrderResource.php`
- `app/Repositories/Eloquent/EloquentOrderRepository.php`

### 4. Rutas
```
feat: add shipping status routes

- Add CRUD routes for shipping statuses management
- Add route to update shipping status from order detail
- Include routes in admin.php
```

**Archivos incluidos:**
- `routes/admin.php`

### 5. Vistas
```
feat: add shipping status admin views

- Create index view for shipping statuses management
- Add shipping status section to order detail view
- Include color badges and status indicators
- Add forms for create, update, and delete operations
```

**Archivos incluidos:**
- `resources/views/admin/shipping-statuses/index.blade.php`
- `resources/views/admin/orders/show.blade.php`

### 6. Seeders
```
feat: add seeders for order and shipping statuses

- Create OrderStatusSeeder with 7 predefined statuses
- Create ShippingStatusSeeder with 7 predefined statuses
- Add OrderStatusFactory for testing
- Update DatabaseSeeder to include both seeders
- Add seed execution scripts for Windows and Linux
```

**Archivos incluidos:**
- `database/seeders/OrderStatusSeeder.php`
- `database/seeders/ShippingStatusSeeder.php`
- `database/factories/OrderStatusFactory.php`
- `database/seeders/DatabaseSeeder.php`
- `seed-status-modules.sh`
- `seed-status-modules.bat`

### 7. Documentación
```
docs: add comprehensive documentation for shipping status module

- Add SHIPPING_STATUS_MODULE.md with full module documentation
- Add SHIPPING_STATUS_INSTALLATION.md with installation guide
- Add SHIPPING_STATUS_EXAMPLES.md with practical examples
- Add ORDER_STATUS_SEEDER.md with seeder documentation
- Add SEEDERS_RESUMEN.md with seeders summary
- Add verification and execution scripts
```

**Archivos incluidos:**
- `SHIPPING_STATUS_MODULE.md`
- `SHIPPING_STATUS_INSTALLATION.md`
- `SHIPPING_STATUS_EXAMPLES.md`
- `ORDER_STATUS_SEEDER.md`
- `SEEDERS_RESUMEN.md`
- `verify-shipping-status-module.php`

## Proceso Manual (Alternativa)

Si prefieres agregar los archivos manualmente, sigue estos pasos:

### 1. Ver archivos modificados
```bash
git status
```

### 2. Agregar archivos por categoría

```bash
# Backend
git add app/Models/ShippingStatus.php
git add app/Http/Controllers/Admin/AdminShippingStatusController.php
git add app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php
git add app/Http/Requests/Admin/StoreShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php
git add app/Http/Resources/ShippingStatusResource.php

# Base de datos
git add database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php
git add database/seeders/ShippingStatusSeeder.php
git add database/seeders/OrderStatusSeeder.php
git add database/factories/ShippingStatusFactory.php
git add database/factories/OrderStatusFactory.php

# Archivos modificados
git add app/Models/Order.php
git add app/Http/Resources/OrderResource.php
git add app/Repositories/Eloquent/EloquentOrderRepository.php
git add routes/admin.php
git add database/seeders/DatabaseSeeder.php

# Vistas
git add resources/views/admin/shipping-statuses/
git add resources/views/admin/orders/show.blade.php

# Documentación
git add SHIPPING_STATUS_MODULE.md
git add SHIPPING_STATUS_INSTALLATION.md
git add SHIPPING_STATUS_EXAMPLES.md
git add ORDER_STATUS_SEEDER.md
git add SEEDERS_RESUMEN.md

# Scripts
git add verify-shipping-status-module.php
git add seed-status-modules.sh
git add seed-status-modules.bat
```

### 3. Crear commits

```bash
# Commit 1
git commit -m "feat: add ShippingStatus model and migration"

# Commit 2
git commit -m "feat: add shipping status controllers and validation"

# Commit 3
git commit -m "feat: integrate ShippingStatus with Order model"

# Commit 4
git commit -m "feat: add shipping status routes"

# Commit 5
git commit -m "feat: add shipping status admin views"

# Commit 6
git commit -m "feat: add seeders for order and shipping statuses"

# Commit 7
git commit -m "docs: add comprehensive documentation for shipping status module"
```

## Después de los Commits

### Verificar commits creados
```bash
git log --oneline -7
```

### Ver detalles de un commit
```bash
git show <commit-hash>
```

### Push al repositorio remoto
```bash
# Push a la rama principal
git push origin main

# O a la rama actual
git push origin HEAD
```

## Convenciones de Commits

Los commits siguen el formato [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - Nueva funcionalidad
- `docs:` - Cambios en documentación
- `fix:` - Corrección de bugs
- `refactor:` - Refactorización de código
- `test:` - Agregar o modificar tests
- `chore:` - Tareas de mantenimiento

## Resumen de Archivos

### Total de Archivos Nuevos: 23

**Backend (10):**
- 1 Modelo
- 2 Controladores
- 3 Requests
- 1 Resource
- 1 Migración
- 2 Seeders
- 2 Factories

**Frontend (1):**
- 1 Vista (index)

**Modificados (5):**
- Order.php
- OrderResource.php
- EloquentOrderRepository.php
- admin.php
- DatabaseSeeder.php
- show.blade.php (orders)

**Documentación (5):**
- SHIPPING_STATUS_MODULE.md
- SHIPPING_STATUS_INSTALLATION.md
- SHIPPING_STATUS_EXAMPLES.md
- ORDER_STATUS_SEEDER.md
- SEEDERS_RESUMEN.md

**Scripts (4):**
- verify-shipping-status-module.php
- seed-status-modules.sh
- seed-status-modules.bat
- git-add-shipping-status-module.sh/bat

## Troubleshooting

### Error: "nothing to commit"
**Causa:** Los archivos ya están commiteados.
**Solución:** Verifica con `git status` si hay cambios pendientes.

### Error: "pathspec did not match any files"
**Causa:** El archivo no existe en la ruta especificada.
**Solución:** Verifica que el archivo exista con `ls` o `dir`.

### Quiero deshacer los commits
```bash
# Deshacer el último commit (mantiene cambios)
git reset --soft HEAD~1

# Deshacer los últimos 7 commits
git reset --soft HEAD~7

# Ver el estado
git status
```

### Quiero modificar el último commit
```bash
# Agregar más archivos al último commit
git add archivo.php
git commit --amend --no-edit

# Cambiar el mensaje del último commit
git commit --amend -m "Nuevo mensaje"
```

## Notas Importantes

- ✅ Los scripts son seguros y no sobrescribirán commits existentes
- ✅ Puedes ejecutar los scripts múltiples veces sin problemas
- ✅ Los commits están organizados lógicamente por funcionalidad
- ⚠️ Asegúrate de estar en la rama correcta antes de ejecutar
- ⚠️ Revisa los commits antes de hacer push al remoto

## Soporte

Si tienes problemas con los scripts o los commits, consulta:
- `git status` - Ver estado actual
- `git log` - Ver historial de commits
- `git diff` - Ver cambios no commiteados
