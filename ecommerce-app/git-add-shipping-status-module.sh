#!/bin/bash

# Script para agregar el módulo de Shipping Status al git
# Uso: bash git-add-shipping-status-module.sh

echo "=========================================="
echo "  Agregando Módulo Shipping Status a Git "
echo "=========================================="
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Verificar que estamos en un repositorio git
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}⚠${NC} No se detectó un repositorio git. Inicializando..."
    git init
fi

echo -e "${BLUE}📦 Agregando archivos del backend...${NC}"

# Modelos
git add app/Models/ShippingStatus.php

# Controladores
git add app/Http/Controllers/Admin/AdminShippingStatusController.php
git add app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php

# Requests
git add app/Http/Requests/Admin/StoreShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateShippingStatusRequest.php
git add app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php

# Resources
git add app/Http/Resources/ShippingStatusResource.php

# Base de datos
git add database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php
git add database/seeders/ShippingStatusSeeder.php
git add database/seeders/OrderStatusSeeder.php
git add database/factories/ShippingStatusFactory.php
git add database/factories/OrderStatusFactory.php

echo -e "${GREEN}✓${NC} Archivos del backend agregados"
echo ""

echo -e "${BLUE}📦 Agregando archivos modificados...${NC}"

# Archivos modificados
git add app/Models/Order.php
git add app/Http/Resources/OrderResource.php
git add app/Repositories/Eloquent/EloquentOrderRepository.php
git add routes/admin.php
git add database/seeders/DatabaseSeeder.php

echo -e "${GREEN}✓${NC} Archivos modificados agregados"
echo ""

echo -e "${BLUE}📦 Agregando vistas...${NC}"

# Vistas
git add resources/views/admin/shipping-statuses/
git add resources/views/admin/orders/show.blade.php

echo -e "${GREEN}✓${NC} Vistas agregadas"
echo ""

echo -e "${BLUE}📦 Agregando documentación...${NC}"

# Documentación
git add SHIPPING_STATUS_MODULE.md
git add SHIPPING_STATUS_INSTALLATION.md
git add SHIPPING_STATUS_EXAMPLES.md
git add ORDER_STATUS_SEEDER.md
git add SEEDERS_RESUMEN.md

echo -e "${GREEN}✓${NC} Documentación agregada"
echo ""

echo -e "${BLUE}📦 Agregando scripts...${NC}"

# Scripts
git add verify-shipping-status-module.php
git add seed-status-modules.sh
git add seed-status-modules.bat
git add git-add-shipping-status-module.sh
git add git-add-shipping-status-module.bat

echo -e "${GREEN}✓${NC} Scripts agregados"
echo ""

echo "=========================================="
echo "  Estado de los archivos agregados       "
echo "=========================================="
echo ""

git status

echo ""
echo "=========================================="
echo "  Creando commits...                      "
echo "=========================================="
echo ""

# Commit 1: Modelo y migración
echo -e "${YELLOW}Commit 1:${NC} Modelo ShippingStatus y migración"
git commit -m "feat: add ShippingStatus model and migration

- Create ShippingStatus model with relationships and scopes
- Add migration to create shipping_statuses table
- Add shipping_status_id column to orders table
- Include 7 predefined shipping statuses
- Add ShippingStatusFactory for testing"

# Commit 2: Controladores y validación
echo -e "${YELLOW}Commit 2:${NC} Controladores y validación"
git commit -m "feat: add shipping status controllers and validation

- Create AdminShippingStatusController for CRUD operations
- Create AdminOrderShippingStatusUpdateController for order updates
- Add request validation classes (Store, Update, UpdateOrder)
- Add ShippingStatusResource for API responses"

# Commit 3: Integración con Order
echo -e "${YELLOW}Commit 3:${NC} Integración con modelo Order"
git commit -m "feat: integrate ShippingStatus with Order model

- Add shippingStatus() relationship to Order model
- Add setShippingStatus() helper method
- Update OrderResource to include shipping_status
- Update EloquentOrderRepository with eager loading
- Add shipping_status_id to fillable fields"

# Commit 4: Rutas
echo -e "${YELLOW}Commit 4:${NC} Rutas del módulo"
git commit -m "feat: add shipping status routes

- Add CRUD routes for shipping statuses management
- Add route to update shipping status from order detail
- Include routes in admin.php"

# Commit 5: Vistas
echo -e "${YELLOW}Commit 5:${NC} Vistas del panel admin"
git commit -m "feat: add shipping status admin views

- Create index view for shipping statuses management
- Add shipping status section to order detail view
- Include color badges and status indicators
- Add forms for create, update, and delete operations"

# Commit 6: Seeders
echo -e "${YELLOW}Commit 6:${NC} Seeders y factories"
git commit -m "feat: add seeders for order and shipping statuses

- Create OrderStatusSeeder with 7 predefined statuses
- Create ShippingStatusSeeder with 7 predefined statuses
- Add OrderStatusFactory for testing
- Update DatabaseSeeder to include both seeders
- Add seed execution scripts for Windows and Linux"

# Commit 7: Documentación
echo -e "${YELLOW}Commit 7:${NC} Documentación completa"
git commit -m "docs: add comprehensive documentation for shipping status module

- Add SHIPPING_STATUS_MODULE.md with full module documentation
- Add SHIPPING_STATUS_INSTALLATION.md with installation guide
- Add SHIPPING_STATUS_EXAMPLES.md with practical examples
- Add ORDER_STATUS_SEEDER.md with seeder documentation
- Add SEEDERS_RESUMEN.md with seeders summary
- Add verification and execution scripts"

echo ""
echo "=========================================="
echo -e "${GREEN}✓ ¡Todos los cambios han sido agregados y commiteados!${NC}"
echo "=========================================="
echo ""
echo "Próximos pasos:"
echo "1. Revisar los commits: git log --oneline -7"
echo "2. Push al repositorio: git push origin main"
echo ""
