# Comandos Útiles para el Checkout

## 🚀 Desarrollo

### Iniciar el entorno de desarrollo

```bash
# Terminal 1: Compilar assets con hot reload
npm run dev

# Terminal 2: Servidor Laravel
php artisan serve
```

### Limpiar cache

```bash
# Limpiar cache de Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar cache de Vite
rm -rf node_modules/.vite
```

## 🧪 Testing

### Probar endpoints de API

```bash
# Obtener carrito
curl http://localhost:8000/api/cart

# Agregar producto al carrito
curl -X POST http://localhost:8000/api/cart/items \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'

# Aplicar cupón
curl -X POST http://localhost:8000/api/cart/coupon \
  -H "Content-Type: application/json" \
  -d '{"code": "DESCUENTO10"}'
```

### Verificar rutas

```bash
# Listar todas las rutas
php artisan route:list

# Filtrar rutas de API
php artisan route:list --path=api

# Filtrar rutas de checkout
php artisan route:list --path=checkout
```

## 🔍 Debugging

### Ver logs en tiempo real

```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de Vite
npm run dev
```

### Inspeccionar base de datos

```bash
# Entrar a MySQL
mysql -u root -p

# Usar base de datos
USE ecommerce_db;

# Ver carritos
SELECT * FROM carts;

# Ver items del carrito
SELECT * FROM cart_items;

# Ver pedidos
SELECT * FROM orders;
```

## 📦 Producción

### Compilar para producción

```bash
# Compilar assets optimizados
npm run build

# Optimizar autoload de Composer
composer install --optimize-autoloader --no-dev

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Verificar compilación

```bash
# Verificar que los assets se compilaron
ls -la public/build/

# Verificar manifest
cat public/build/manifest.json
```

## 🔧 Mantenimiento

### Actualizar dependencias

```bash
# Actualizar dependencias de NPM
npm update

# Actualizar dependencias de Composer
composer update
```

### Regenerar assets

```bash
# Eliminar build anterior
rm -rf public/build

# Recompilar
npm run build
```

## 🗄️ Base de Datos

### Crear tablas necesarias

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar migraciones con seeders
php artisan migrate --seed

# Refrescar base de datos (CUIDADO: elimina datos)
php artisan migrate:fresh --seed
```

### Crear datos de prueba

```bash
# Ejecutar seeders específicos
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=CategorySeeder
```

## 🔐 Sanctum

### Configurar Sanctum

```bash
# Publicar configuración de Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Ejecutar migraciones de Sanctum
php artisan migrate
```

### Limpiar tokens

```bash
# Eliminar tokens expirados
php artisan sanctum:prune-expired
```

## 📊 Monitoreo

### Ver estado del sistema

```bash
# Ver información de PHP
php -v

# Ver información de Node
node -v
npm -v

# Ver información de Composer
composer -V

# Ver información de Laravel
php artisan --version
```

### Verificar permisos

```bash
# Dar permisos a storage y cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🐛 Solución de Problemas

### Error 500 - Internal Server Error

```bash
# Ver logs detallados
tail -f storage/logs/laravel.log

# Verificar permisos
ls -la storage/

# Limpiar cache
php artisan cache:clear
php artisan config:clear
```

### Error 419 - CSRF Token Mismatch

```bash
# Limpiar sesiones
php artisan session:clear

# Verificar configuración de Sanctum
php artisan config:show sanctum

# Regenerar key de aplicación
php artisan key:generate
```

### Assets no se cargan

```bash
# Verificar que Vite esté corriendo
ps aux | grep vite

# Verificar puerto de Vite
lsof -i :5173

# Recompilar assets
npm run build
```

### Base de datos no conecta

```bash
# Verificar conexión
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar configuración
php artisan config:show database
```

## 🔄 Git

### Comandos útiles

```bash
# Ver estado
git status

# Ver cambios
git diff

# Agregar archivos del checkout
git add resources/js/components/checkout/
git add resources/js/Pages/CheckoutPage.vue
git add resources/js/stores/checkout.js

# Commit
git commit -m "feat: Implementar sistema de checkout Vue 3"

# Push
git push origin main
```

## 📝 Logs

### Ver diferentes tipos de logs

```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de acceso de Apache
tail -f /var/log/apache2/access.log

# Logs de error de Apache
tail -f /var/log/apache2/error.log

# Logs de Nginx
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

## 🧹 Limpieza

### Limpiar todo

```bash
# Limpiar cache de Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar cache de Composer
composer clear-cache

# Limpiar node_modules y reinstalar
rm -rf node_modules
npm install

# Limpiar build de Vite
rm -rf public/build
npm run build
```

## 📱 Testing en Dispositivos Móviles

### Exponer servidor local

```bash
# Usar ngrok para exponer localhost
ngrok http 8000

# O usar el servidor de Laravel con IP específica
php artisan serve --host=0.0.0.0 --port=8000
```

### Verificar en red local

```bash
# Obtener IP local
ip addr show

# Acceder desde otro dispositivo
# http://TU_IP_LOCAL:8000/checkout
```

## 🎯 Comandos Rápidos

```bash
# Setup completo desde cero
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev

# Reiniciar todo
php artisan cache:clear && php artisan config:clear && npm run build

# Ver estado del checkout
php artisan route:list --path=checkout
curl http://localhost:8000/api/cart

# Verificar setup
./verify-checkout-setup.sh
```

## 📚 Recursos

- Laravel Docs: https://laravel.com/docs
- Vue 3 Docs: https://vuejs.org/
- Pinia Docs: https://pinia.vuejs.org/
- Sanctum Docs: https://laravel.com/docs/sanctum
- TailwindCSS Docs: https://tailwindcss.com/

---

**Tip**: Guarda este archivo como referencia rápida durante el desarrollo.
