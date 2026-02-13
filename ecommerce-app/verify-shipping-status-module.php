<?php

/**
 * Script de Verificación del Módulo de Shipping Status
 * 
 * Ejecutar con: php verify-shipping-status-module.php
 */

echo "=== Verificación del Módulo de Shipping Status ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// Verificar archivos del backend
$backendFiles = [
    'app/Models/ShippingStatus.php',
    'app/Http/Controllers/Admin/AdminShippingStatusController.php',
    'app/Http/Controllers/Admin/AdminOrderShippingStatusUpdateController.php',
    'app/Http/Requests/Admin/StoreShippingStatusRequest.php',
    'app/Http/Requests/Admin/UpdateShippingStatusRequest.php',
    'app/Http/Requests/Admin/UpdateOrderShippingStatusRequest.php',
    'app/Http/Resources/ShippingStatusResource.php',
    'database/migrations/2026_02_13_000002_create_shipping_statuses_and_add_shipping_status_id_to_orders_table.php',
    'database/seeders/ShippingStatusSeeder.php',
    'database/factories/ShippingStatusFactory.php',
];

echo "Verificando archivos del backend...\n";
foreach ($backendFiles as $file) {
    if (file_exists($file)) {
        $success[] = "✅ $file";
    } else {
        $errors[] = "❌ $file - NO ENCONTRADO";
    }
}

// Verificar archivos del frontend
$frontendFiles = [
    'resources/views/admin/shipping-statuses/index.blade.php',
];

echo "\nVerificando archivos del frontend...\n";
foreach ($frontendFiles as $file) {
    if (file_exists($file)) {
        $success[] = "✅ $file";
    } else {
        $errors[] = "❌ $file - NO ENCONTRADO";
    }
}

// Verificar archivos modificados
$modifiedFiles = [
    'app/Models/Order.php',
    'app/Http/Resources/OrderResource.php',
    'app/Repositories/Eloquent/EloquentOrderRepository.php',
    'routes/admin.php',
    'resources/views/admin/orders/show.blade.php',
];

echo "\nVerificando archivos modificados...\n";
foreach ($modifiedFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Verificaciones específicas
        if ($file === 'app/Models/Order.php') {
            if (strpos($content, 'shippingStatus()') !== false && strpos($content, 'setShippingStatus') !== false) {
                $success[] = "✅ $file - Contiene métodos necesarios";
            } else {
                $warnings[] = "⚠️ $file - Puede faltar métodos shippingStatus() o setShippingStatus()";
            }
        } elseif ($file === 'routes/admin.php') {
            if (strpos($content, 'AdminShippingStatusController') !== false) {
                $success[] = "✅ $file - Contiene rutas de shipping status";
            } else {
                $errors[] = "❌ $file - No contiene rutas de shipping status";
            }
        } else {
            $success[] = "✅ $file - Existe";
        }
    } else {
        $errors[] = "❌ $file - NO ENCONTRADO";
    }
}

// Verificar documentación
$docFiles = [
    'SHIPPING_STATUS_MODULE.md',
    'SHIPPING_STATUS_INSTALLATION.md',
];

echo "\nVerificando documentación...\n";
foreach ($docFiles as $file) {
    if (file_exists($file)) {
        $success[] = "✅ $file";
    } else {
        $warnings[] = "⚠️ $file - NO ENCONTRADO";
    }
}

// Mostrar resultados
echo "\n=== RESULTADOS ===\n\n";

if (!empty($success)) {
    echo "ÉXITOS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "ADVERTENCIAS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "ERRORES (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

// Resumen final
$total = count($backendFiles) + count($frontendFiles) + count($modifiedFiles) + count($docFiles);
$found = count($success);

echo "=== RESUMEN ===\n";
echo "Total de archivos verificados: $total\n";
echo "Archivos encontrados: $found\n";
echo "Errores: " . count($errors) . "\n";
echo "Advertencias: " . count($warnings) . "\n";

if (count($errors) === 0) {
    echo "\n✅ ¡Módulo instalado correctamente!\n";
    echo "\nPróximos pasos:\n";
    echo "1. Ejecutar: php artisan migrate\n";
    echo "2. Acceder a: /admin/shipping-statuses\n";
    echo "3. Revisar la documentación en SHIPPING_STATUS_MODULE.md\n";
} else {
    echo "\n❌ Hay errores que deben corregirse antes de usar el módulo.\n";
    exit(1);
}
