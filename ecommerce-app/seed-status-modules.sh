#!/bin/bash

# Script para ejecutar los seeders de estatus
# Uso: bash seed-status-modules.sh

echo "==================================="
echo "  Seeders de Estatus - Ecommerce  "
echo "==================================="
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "Error: No se encuentra el archivo 'artisan'. Asegúrate de estar en el directorio raíz del proyecto Laravel."
    exit 1
fi

print_success "Directorio del proyecto verificado"
echo ""

# Preguntar qué seeders ejecutar
echo "¿Qué seeders deseas ejecutar?"
echo "1) Solo OrderStatusSeeder"
echo "2) Solo ShippingStatusSeeder"
echo "3) Ambos seeders"
echo "4) Todos los seeders (DatabaseSeeder)"
echo ""
read -p "Selecciona una opción (1-4): " option

case $option in
    1)
        echo ""
        print_warning "Ejecutando OrderStatusSeeder..."
        php artisan db:seed --class=OrderStatusSeeder
        
        if [ $? -eq 0 ]; then
            print_success "OrderStatusSeeder ejecutado correctamente"
        else
            print_error "Error al ejecutar OrderStatusSeeder"
            exit 1
        fi
        ;;
    2)
        echo ""
        print_warning "Ejecutando ShippingStatusSeeder..."
        php artisan db:seed --class=ShippingStatusSeeder
        
        if [ $? -eq 0 ]; then
            print_success "ShippingStatusSeeder ejecutado correctamente"
        else
            print_error "Error al ejecutar ShippingStatusSeeder"
            exit 1
        fi
        ;;
    3)
        echo ""
        print_warning "Ejecutando OrderStatusSeeder..."
        php artisan db:seed --class=OrderStatusSeeder
        
        if [ $? -eq 0 ]; then
            print_success "OrderStatusSeeder ejecutado correctamente"
        else
            print_error "Error al ejecutar OrderStatusSeeder"
            exit 1
        fi
        
        echo ""
        print_warning "Ejecutando ShippingStatusSeeder..."
        php artisan db:seed --class=ShippingStatusSeeder
        
        if [ $? -eq 0 ]; then
            print_success "ShippingStatusSeeder ejecutado correctamente"
        else
            print_error "Error al ejecutar ShippingStatusSeeder"
            exit 1
        fi
        ;;
    4)
        echo ""
        print_warning "Ejecutando todos los seeders (DatabaseSeeder)..."
        php artisan db:seed
        
        if [ $? -eq 0 ]; then
            print_success "Todos los seeders ejecutados correctamente"
        else
            print_error "Error al ejecutar los seeders"
            exit 1
        fi
        ;;
    *)
        print_error "Opción inválida"
        exit 1
        ;;
esac

echo ""
echo "==================================="
echo "  Verificando resultados...        "
echo "==================================="
echo ""

# Verificar OrderStatus
echo "Estatus de Órdenes:"
php artisan tinker --execute="echo 'Total: ' . \App\Models\OrderStatus::count() . PHP_EOL; \App\Models\OrderStatus::all(['name', 'slug', 'is_default'])->each(function(\$s) { echo '  - ' . \$s->name . ' (' . \$s->slug . ')' . (\$s->is_default ? ' [DEFAULT]' : '') . PHP_EOL; });"

echo ""

# Verificar ShippingStatus
echo "Estatus de Envíos:"
php artisan tinker --execute="echo 'Total: ' . \App\Models\ShippingStatus::count() . PHP_EOL; \App\Models\ShippingStatus::ordered()->get(['name', 'slug', 'is_default', 'sort_order'])->each(function(\$s) { echo '  - ' . \$s->name . ' (' . \$s->slug . ') [Orden: ' . \$s->sort_order . ']' . (\$s->is_default ? ' [DEFAULT]' : '') . PHP_EOL; });"

echo ""
print_success "¡Proceso completado!"
echo ""
