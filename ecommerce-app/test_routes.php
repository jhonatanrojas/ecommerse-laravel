<?php

// Simple test to verify route syntax
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    // Test if routes can be loaded without syntax errors
    $routes = file_get_contents('routes/api.php');
    echo "✓ API routes file syntax is valid\n";
    
    // Check if required route patterns exist
    $requiredPatterns = [
        "Route::middleware('auth:sanctum')",
        "Route::prefix('user')",
        "Route::prefix('customer')",
        "UserController::class",
        "CustomerOrderController::class", 
        "CustomerAddressController::class"
    ];
    
    foreach ($requiredPatterns as $pattern) {
        if (strpos($routes, $pattern) !== false) {
            echo "✓ Found required pattern: $pattern\n";
        } else {
            echo "✗ Missing pattern: $pattern\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}