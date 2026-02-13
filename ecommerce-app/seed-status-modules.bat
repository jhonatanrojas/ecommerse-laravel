@echo off
REM Script para ejecutar los seeders de estatus en Windows
REM Uso: seed-status-modules.bat

echo ===================================
echo   Seeders de Estatus - Ecommerce  
echo ===================================
echo.

REM Verificar que estamos en el directorio correcto
if not exist "artisan" (
    echo [ERROR] No se encuentra el archivo 'artisan'. Asegurate de estar en el directorio raiz del proyecto Laravel.
    exit /b 1
)

echo [OK] Directorio del proyecto verificado
echo.

REM Preguntar qué seeders ejecutar
echo Que seeders deseas ejecutar?
echo 1^) Solo OrderStatusSeeder
echo 2^) Solo ShippingStatusSeeder
echo 3^) Ambos seeders
echo 4^) Todos los seeders ^(DatabaseSeeder^)
echo.
set /p option="Selecciona una opcion (1-4): "

if "%option%"=="1" (
    echo.
    echo [INFO] Ejecutando OrderStatusSeeder...
    php artisan db:seed --class=OrderStatusSeeder
    
    if errorlevel 1 (
        echo [ERROR] Error al ejecutar OrderStatusSeeder
        exit /b 1
    )
    echo [OK] OrderStatusSeeder ejecutado correctamente
    goto verify
)

if "%option%"=="2" (
    echo.
    echo [INFO] Ejecutando ShippingStatusSeeder...
    php artisan db:seed --class=ShippingStatusSeeder
    
    if errorlevel 1 (
        echo [ERROR] Error al ejecutar ShippingStatusSeeder
        exit /b 1
    )
    echo [OK] ShippingStatusSeeder ejecutado correctamente
    goto verify
)

if "%option%"=="3" (
    echo.
    echo [INFO] Ejecutando OrderStatusSeeder...
    php artisan db:seed --class=OrderStatusSeeder
    
    if errorlevel 1 (
        echo [ERROR] Error al ejecutar OrderStatusSeeder
        exit /b 1
    )
    echo [OK] OrderStatusSeeder ejecutado correctamente
    
    echo.
    echo [INFO] Ejecutando ShippingStatusSeeder...
    php artisan db:seed --class=ShippingStatusSeeder
    
    if errorlevel 1 (
        echo [ERROR] Error al ejecutar ShippingStatusSeeder
        exit /b 1
    )
    echo [OK] ShippingStatusSeeder ejecutado correctamente
    goto verify
)

if "%option%"=="4" (
    echo.
    echo [INFO] Ejecutando todos los seeders ^(DatabaseSeeder^)...
    php artisan db:seed
    
    if errorlevel 1 (
        echo [ERROR] Error al ejecutar los seeders
        exit /b 1
    )
    echo [OK] Todos los seeders ejecutados correctamente
    goto verify
)

echo [ERROR] Opcion invalida
exit /b 1

:verify
echo.
echo ===================================
echo   Verificando resultados...        
echo ===================================
echo.

echo Estatus de Ordenes:
php artisan tinker --execute="echo 'Total: ' . \App\Models\OrderStatus::count() . PHP_EOL; \App\Models\OrderStatus::all(['name', 'slug', 'is_default'])->each(function($s) { echo '  - ' . $s->name . ' (' . $s->slug . ')' . ($s->is_default ? ' [DEFAULT]' : '') . PHP_EOL; });"

echo.

echo Estatus de Envios:
php artisan tinker --execute="echo 'Total: ' . \App\Models\ShippingStatus::count() . PHP_EOL; \App\Models\ShippingStatus::ordered()->get(['name', 'slug', 'is_default', 'sort_order'])->each(function($s) { echo '  - ' . $s->name . ' (' . $s->slug . ') [Orden: ' . $s->sort_order . ']' . ($s->is_default ? ' [DEFAULT]' : '') . PHP_EOL; });"

echo.
echo [OK] Proceso completado!
echo.
pause
