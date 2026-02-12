# Autenticación y autorización: guards Admin y Customer

## Resumen

El proyecto separa por completo la autenticación de **administradores** y **clientes** mediante dos guards de sesión y el uso de Sanctum para la API de clientes.

---

## 1. Configuración de autenticación (`config/auth.php`)

### Defaults
- **guard**: `customer` — Por defecto la app asume contexto de cliente (ecommerce).
- **passwords**: `customers` — Reseteo de contraseña por defecto para clientes.

### Guards

| Guard     | Driver  | Provider  | Uso |
|-----------|---------|-----------|-----|
| **admin** | session | admins    | Solo panel administrativo. Login en `/admin/login`. Sesión independiente de la del cliente. |
| **customer** | session | customers | Ecommerce web: login/registro en `/login`, `/register`, área de cliente. |
| **web**   | session | customers | Alias de customer (compatibilidad). |
| **sanctum** | sanctum | customers | API: autenticación por token (Bearer). Usado por `auth:sanctum` en rutas `/api/*`. |

### Providers
- **admins** y **customers** usan el mismo modelo `App\Models\User` y la misma tabla `users`.
- La separación es por **contexto**: los administradores tienen roles/permisos (Spatie, guard `admin`); los clientes usan el mismo User pero sin roles de ese guard para el flujo de tienda.

### Passwords
- `users`, `admins` y `customers` tienen entrada en `passwords` para poder usar reseteo de contraseña por guard si se desea en el futuro.

---

## 2. Spatie Permission (`config/permission.php`)

- Los **roles y permisos** se usan **solo para el guard `admin`**.
- El modelo `User` define `$guard_name = 'admin'` y sobrescribe `getDefaultGuardName()` para devolver `'admin'`, de modo que todas las asignaciones/consultas de roles y permisos usen ese guard.
- Los clientes **no tienen** roles de Spatie en el flujo de la tienda; el guard `customer` no interviene en permisos.
- En rutas admin puedes seguir usando los middlewares `role` y `permission` de Spatie; por defecto usarán el guard del usuario autenticado (admin).

---

## 3. Middlewares

### `auth:admin`
- Protege rutas del panel admin (`/admin/*` salvo login).
- Redirección si no autenticado: `route('admin.login')` (definida en `Authenticate` según ruta que empiece por `admin`).

### `auth:customer`
- Protege rutas de cliente (dashboard, perfil, etc.).
- Redirección si no autenticado: `route('login')`.

### `guest:admin`
- Para la pantalla de login admin: si ya está autenticado como admin, redirige a `route('admin.dashboard')`.

### `guest:customer`
- Para login/registro de clientes: si ya está autenticado como customer, redirige a `RouteServiceProvider::HOME` (`/customer`).

### `role` y `permission` (Spatie)
- Se usan en rutas admin; funcionan con el guard `admin` gracias a la configuración del modelo `User`.

---

## 4. Rutas

### Admin (`routes/admin.php`)
- **GET/POST** `/admin/login` — Login exclusivo administradores (middleware `guest:admin`).
- **POST** `/admin/logout` — Cerrar sesión admin (middleware `auth:admin`).
- **GET** `/admin/dashboard` — Dashboard del panel (middleware `auth:admin`, `verified`).
- Resto de rutas bajo `prefix('admin')` con middleware `auth:admin` y `verified`.

### Web cliente (`routes/web.php` + `routes/auth.php`)
- **GET/POST** `/login`, **GET/POST** `/register` — Solo clientes (middleware `guest:customer`).
- **POST** `/logout` — Cerrar sesión cliente (definido en `auth.php`, middleware `auth:customer`).
- **GET** `/customer` — Dashboard cliente (middleware `auth:customer`, `verified`).
- Perfil y verificación de email usan `auth:customer`.

### API (`routes/api.php`)
- **POST** `/api/register`, **POST** `/api/login` — Públicas; generan token para cliente.
- Rutas que requieren autenticación: **middleware `auth:sanctum`** (token Bearer). El usuario resuelto es el mismo User (cliente); no se usa sesión admin en la API.

---

## 5. Controladores

### `App\Http\Controllers\Admin\AdminAuthController`
- **showLoginForm()** — Muestra `admin.login` (vista `admin/login.blade.php`).
- **login(Request)** — `Auth::guard('admin')->attempt()`. Tras login comprueba que el usuario tenga al menos un rol (Spatie); si no, cierra sesión y devuelve error. Redirige a `RouteServiceProvider::ADMIN_HOME` (`/admin/dashboard`).
- **logout(Request)** — `Auth::guard('admin')->logout()`, invalida sesión y redirige a `route('admin.login')`.

### Cliente (web)
- **AuthenticatedSessionController** — Login con guard `customer` (vía `LoginRequest`), logout con `Auth::guard('customer')->logout()`.
- **RegisteredUserController** — Registro y `Auth::guard('customer')->login($user)`.
- **LoginRequest** — `Auth::guard('customer')->attempt()`.

### Cliente (API)
- **AuthController** — `register`, `login`, `logout`. El login usa `Auth::guard('customer')->attempt()` para validar credenciales; luego se genera token con `$user->createToken()`. No se usa sesión; el flujo es independiente del admin.

---

## 6. Vistas

- **admin/login.blade.php** — Formulario de login del panel admin (email, password, recordarme). No se mezcla con las vistas de cliente.
- Las vistas del panel admin (dashboard, CRUD, etc.) siguen usando el layout admin y las rutas con prefijo y nombre `admin.*`.
- Login y registro de clientes siguen en `auth/login.blade.php` y `auth/register.blade.php` (y/o SPA según tu front).

---

## 7. Cómo funciona cada guard

### Guard **admin**
- **Cuándo se usa**: Solo al entrar por `/admin/login` y al navegar por `/admin/*`.
- **Driver**: sesión (cookie de sesión Laravel).
- **Provider**: `admins` → modelo `User`.
- **Efecto**: Una sesión “admin” separada. Un usuario puede tener sesión de admin abierta y, en otra pestaña, sesión de cliente (o no tenerla). No se pisan.
- **Roles/permisos**: Spatie usa guard `admin` para ese User; solo usuarios con al menos un rol pueden completar el login admin.

### Guard **customer**
- **Cuándo se usa**: En `/login`, `/register`, `/customer`, perfil, y en cualquier ruta web que use `auth:customer` o el guard por defecto.
- **Driver**: sesión.
- **Provider**: `customers` → modelo `User`.
- **Efecto**: Sesión “cliente” para la tienda. Independiente de la sesión admin.

### Sanctum (API)
- **Cuándo se usa**: En rutas `api` con middleware `auth:sanctum`.
- **Efecto**: No usa sesión; el usuario se identifica por token Bearer. El token está asociado al mismo modelo `User`; típicamente se crea en el login/registro de la API (cliente). Los administradores no usan tokens para el panel; usan sesión con guard `admin`.

---

## 8. Verificación de integridad

- **Panel admin**: Acceso solo por `/admin/login`; rutas `/admin/*` protegidas con `auth:admin`; roles/permisos con guard `admin`.
- **Clientes**: Login/registro en `/login` y `/register`; dashboard en `/customer`; perfil y logout cliente sin mezcla con admin.
- **API**: Login/register devuelven token; rutas protegidas con `auth:sanctum`; checkout y carrito siguen funcionando con el usuario autenticado por token o sesión según corresponda.
- **Carrito y checkout**: Siguen usando el usuario autenticado (cliente por sesión o por token); no dependen del guard admin.
- **Migración**: `2026_02_12_000000_set_permission_guard_to_admin.php` actualiza `guard_name` de roles y permisos existentes a `admin` para alinear con Spatie.

---

## Archivos modificados/creados (referencia)

- `config/auth.php` — Guards y providers.
- `config/permission.php` — Comentario sobre guard admin (lógica en modelo User).
- `config/sanctum.php` — Guard `customer` en el array `guard`.
- `app/Models/User.php` — `$guard_name`, `getDefaultGuardName()`.
- `app/Http/Middleware/Authenticate.php` — Redirección según ruta (admin vs login).
- `app/Http/Middleware/RedirectIfAuthenticated.php` — Redirección por guard (admin vs HOME).
- `app/Providers/RouteServiceProvider.php` — `HOME`, `ADMIN_HOME`.
- `app/Http/Controllers/Admin/AdminAuthController.php` — Login/logout admin.
- `routes/admin.php` — Rutas login admin, logout, dashboard y resto con `auth:admin`.
- `routes/web.php` — Customer dashboard y perfil con `auth:customer`.
- `routes/auth.php` — `guest:customer` y `auth:customer`.
- `resources/views/admin/login.blade.php` — Vista login admin.
- Vistas/layouts admin actualizados para `admin.dashboard` y `admin.logout`.
- `database/seeders/RolePermissionSeeder.php` y `UserModuleSeeder.php` — Guard `admin` en roles/permisos.
- `database/migrations/2026_02_12_000000_set_permission_guard_to_admin.php` — Ajuste de `guard_name` en tablas de permisos.
