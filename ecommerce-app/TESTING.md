# Ejecutar tests

## Requisitos de base de datos

Los tests usan **SQLite en memoria** por defecto (configurado en `phpunit.xml`).

### Opción 1: SQLite (recomendado para CI y desarrollo)

Instala la extensión PHP SQLite:

```bash
# Ubuntu/Debian
sudo apt-get install php-sqlite3

# O habilitar en php.ini: extension=pdo_sqlite
```

Luego:

```bash
php artisan test
```

### Opción 2: MySQL

Si no tienes SQLite y usas MySQL:

1. Crea `.env.testing` a partir de `.env` y configura la base de datos de pruebas:
   ```
   APP_ENV=testing
   DB_CONNECTION=mysql
   DB_DATABASE=ecommerce_test
   # ... resto igual que tu .env
   ```

2. Comenta o elimina las líneas `DB_CONNECTION` y `DB_DATABASE` en la sección `<php>` de `phpunit.xml` para que se use `.env.testing`.

3. Crea la base de datos y ejecuta migraciones:
   ```bash
   php artisan migrate --env=testing
   php artisan test
   ```

## Ejecutar todos los tests

```bash
cd ecommerce-app
php artisan test
```

## Ejecutar un grupo de tests

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test tests/Feature/Api/UserControllerTest.php
```
