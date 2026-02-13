@echo off
REM Script para agregar el módulo de Shipping Status al git (Windows)
REM Uso: git-add-shipping-status-module.bat

echo ==========================================
echo   Agregando Modulo Shipping Status a Git 
echo ==========================================
echo.

REM Verificar que estamos en un repositorio git
if not exist ".git" (
    echo [INFO] No se detecto un repositorio git. Inicializando...
    git init
)

echo [INFO] Agregando archivos del backend...

REM Modelos
git add app/Models/ShippingStatus.php

REM Controladores
git add app/Http/Controllers/Admin/AdminShippingStatusController.php
git add app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php

REM Requests
git add app/Http/Requests/Admin/StoreShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php

REM Resources
git add app/Http/Resources/ShippingStatusResource.php

REM Base de datos
git add database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php
git add database/seeders/ShippingStatusSeeder.php
git add database/seeders/OrderStatusSeeder.php
git add database/factories/ShippingStatusFactory.php
git add database/factories/OrderStatusFactory.php

echo [OK] Archivos del backend agregados
echo.

echo [INFO] Agregando archivos modificados...

REM Archivos modificados
git add app/Models/Order.php
git add app/Http/Resources/OrderResource.php
git add app/Repositories/Eloquent/EloquentOrderRepository.php
git add routes/admin.php
git add database/seeders/DatabaseSeeder.php

echo [OK] Archivos modificados agregados
echo.

echo [INFO] Agregando vistas...

REM Vistas
git add resources/views/admin/shipping-statuses/
git add resources/views/admin/orders/show.blade.php

echo [OK] Vistas agregadas
echo.

echo [INFO] Agregando documentacion...

REM Documentación
git add SHIPPING_STATUS_MODULE.md
git add SHIPPING_STATUS_INSTALLATION.md
git add SHIPPING_STATUS_EXAMPLES.md
git add ORDER_STATUS_SEEDER.md
git add SEEDERS_RESUMEN.md

echo [OK] Documentacion agregada
echo.

echo [INFO] Agregando scripts...

REM Scripts
git add verify-shipping-status-module.php
git add seed-status-modules.sh
git add seed-status-modules.bat
git add git-add-shipping-status-module.sh
git add git-add-shipping-status-module.bat

echo [OK] Scripts agregados
echo.

echo ==========================================
echo   Estado de los archivos agregados       
echo ==========================================
echo.

git status

echo.
echo ==========================================
echo   Creando commits...                      
echo ==========================================
echo.

REM Commit 1: Modelo y migración
echo [Commit 1] Modelo ShippingStatus y migracion
git commit -m "feat: add ShippingStatus model and migration" -m "- Create ShippingStatus model with relationships and scopes" -m "- Add migration to create shipping_statuses table" -m "- Add shipping_status_id column to orders table" -m "- Include 7 predefined shipping statuses" -m "- Add ShippingStatusFactory for testing"

REM Commit 2: Controladores y validación
echo [Commit 2] Controladores y validacion
git commit -m "feat: add shipping status controllers and validation" -m "- Create AdminShippingStatusController for CRUD operations" -m "- Create AdminOrderShippingStatusUpdateController for order updates" -m "- Add request validation classes (Store, Update, UpdateOrder)" -m "- Add ShippingStatusResource for API responses"

REM Commit 3: Integración con Order
echo [Commit 3] Integracion con modelo Order
git commit -m "feat: integrate ShippingStatus with Order model" -m "- Add shippingStatus() relationship to Order model" -m "- Add setShippingStatus() helper method" -m "- Update OrderResource to include shipping_status" -m "- Update EloquentOrderRepository with eager loading" -m "- Add shipping_status_id to fillable fields"

REM Commit 4: Rutas
echo [Commit 4] Rutas del modulo
git commit -m "feat: add shipping status routes" -m "- Add CRUD routes for shipping statuses management" -m "- Add route to update shipping status from order detail" -m "- Include routes in admin.php"

REM Commit 5: Vistas
echo [Commit 5] Vistas del panel admin
git commit -m "feat: add shipping status admin views" -m "- Create index view for shipping statuses management" -m "- Add shipping status section to order detail view" -m "- Include color badges and status indicators" -m "- Add forms for create, update, and delete operations"

REM Commit 6: Seeders
echo [Commit 6] Seeders y factories
git commit -m "feat: add seeders for order and shipping statuses" -m "- Create OrderStatusSeeder with 7 predefined statuses" -m "- Create ShippingStatusSeeder with 7 predefined statuses" -m "- Add OrderStatusFactory for testing" -m "- Update DatabaseSeeder to include both seeders" -m "- Add seed execution scripts for Windows and Linux"

REM Commit 7: Documentación
echo [Commit 7] Documentacion completa
git commit -m "docs: add comprehensive documentation for shipping status module" -m "- Add SHIPPING_STATUS_MODULE.md with full module documentation" -m "- Add SHIPPING_STATUS_INSTALLATION.md with installation guide" -m "- Add SHIPPING_STATUS_EXAMPLES.md with practical examples" -m "- Add ORDER_STATUS_SEEDER.md with seeder documentation" -m "- Add SEEDERS_RESUMEN.md with seeders summary" -m "- Add verification and execution scripts"

echo.
echo ==========================================
echo [OK] Todos los cambios han sido agregados y commiteados!
echo ==========================================
echo.
echo Proximos pasos:
echo 1. Revisar los commits: git log --oneline -7
echo 2. Push al repositorio: git push origin main
echo.
pause
