#!/bin/bash

echo "🔍 Verificando configuración del Checkout Vue 3..."
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check counter
CHECKS_PASSED=0
CHECKS_FAILED=0

# Function to check file exists
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $2"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $2 - Archivo no encontrado: $1"
        ((CHECKS_FAILED++))
    fi
}

# Function to check directory exists
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $2"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $2 - Directorio no encontrado: $1"
        ((CHECKS_FAILED++))
    fi
}

# Function to check string in file
check_string_in_file() {
    if grep -q "$2" "$1" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} $3"
        ((CHECKS_PASSED++))
    else
        echo -e "${RED}✗${NC} $3 - No encontrado en: $1"
        ((CHECKS_FAILED++))
    fi
}

echo "📁 Verificando estructura de archivos..."
echo ""

# Check Vue components
check_dir "resources/js/components/checkout" "Directorio de componentes checkout"
check_file "resources/js/components/checkout/ShippingAddressForm.vue" "ShippingAddressForm.vue"
check_file "resources/js/components/checkout/BillingAddressForm.vue" "BillingAddressForm.vue"
check_file "resources/js/components/checkout/ShippingMethods.vue" "ShippingMethods.vue"
check_file "resources/js/components/checkout/PaymentMethods.vue" "PaymentMethods.vue"
check_file "resources/js/components/checkout/OrderSummary.vue" "OrderSummary.vue"
check_file "resources/js/components/checkout/CustomerDataSection.vue" "CustomerDataSection.vue"
check_file "resources/js/components/checkout/CheckoutActions.vue" "CheckoutActions.vue"

echo ""
echo "📄 Verificando páginas Vue..."
echo ""

check_file "resources/js/Pages/CheckoutPage.vue" "CheckoutPage.vue"
check_file "resources/js/Pages/OrderSuccess.vue" "OrderSuccess.vue"

echo ""
echo "🏪 Verificando stores y servicios..."
echo ""

check_file "resources/js/stores/checkout.js" "Checkout Store"
check_file "resources/js/services/api.js" "API Service"
check_file "resources/js/services/checkoutService.js" "Checkout Service"

echo ""
echo "🛣️ Verificando router..."
echo ""

check_file "resources/js/router/index.js" "Vue Router"
check_file "resources/js/checkout-app.js" "Checkout App Entry Point"

echo ""
echo "📝 Verificando tipos..."
echo ""

check_file "resources/js/types/checkout.js" "Type Definitions"

echo ""
echo "🎨 Verificando vistas Blade..."
echo ""

check_file "resources/views/checkout.blade.php" "Vista Blade de Checkout"

echo ""
echo "⚙️ Verificando configuración..."
echo ""

check_string_in_file "vite.config.js" "checkout-app.js" "Vite configurado con checkout-app.js"
check_string_in_file "routes/web.php" "/checkout" "Ruta /checkout en web.php"
check_string_in_file "routes/web.php" "order-success" "Ruta order-success en web.php"

echo ""
echo "🔌 Verificando backend..."
echo ""

check_file "app/Http/Controllers/Api/CheckoutController.php" "CheckoutController"
check_file "app/Http/Requests/Cart/CheckoutRequest.php" "CheckoutRequest"
check_string_in_file "routes/api.php" "cart/checkout" "Ruta API /cart/checkout"

echo ""
echo "📊 Resumen de verificación"
echo "=========================="
echo -e "Verificaciones exitosas: ${GREEN}$CHECKS_PASSED${NC}"
echo -e "Verificaciones fallidas: ${RED}$CHECKS_FAILED${NC}"
echo ""

if [ $CHECKS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ ¡Todas las verificaciones pasaron!${NC}"
    echo ""
    echo "🚀 Próximos pasos:"
    echo "1. Ejecutar: npm install"
    echo "2. Ejecutar: npm run dev"
    echo "3. Navegar a: http://localhost:8000/checkout"
    echo ""
    exit 0
else
    echo -e "${RED}✗ Algunas verificaciones fallaron${NC}"
    echo ""
    echo "Por favor revisa los archivos faltantes arriba."
    echo ""
    exit 1
fi
