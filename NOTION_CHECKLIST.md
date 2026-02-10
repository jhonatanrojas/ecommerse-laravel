# Checklist Configuración Inicial - E-commerce Laravel

Usa este documento para actualizar las tareas en tu Notion. **Marca como completadas** las que ya están hechas.

---

## ✅ Tareas completadas (marcar en Notion)

### Configuración Inicial
- [x] Laravel 10+ instalado
- [x] Eloquent configurado (modelos con relaciones)
- [x] Sanctum instalado y configurado para API
- [x] Redis configurado como driver de caché
- [x] Base de datos MySQL configurada
- [x] Migraciones: products, orders, order_items
- [x] Seeders: ProductSeeder
- [x] Estructura de carpetas según especificación

### Módulo Productos
- [x] Modelo Product con relaciones
- [x] ProductRepository + ProductRepositoryInterface
- [x] ProductService
- [x] ProductController (CRUD)
- [x] StoreProductRequest / UpdateProductRequest
- [x] ProductPolicy
- [x] Rutas API de productos
- [x] ProductControllerTest (Feature)
- [x] ProductServiceTest (Unit)

### Autenticación API
- [x] AuthController (register, login, logout)
- [x] Rutas: POST /api/register, POST /api/login, POST /api/logout

---

## Pendientes (opcionales o siguientes fases)

- [ ] Módulo de Órdenes (OrderController, etc.)
- [ ] Frontend con Vue.js
- [ ] Configurar CORS para producción (actualmente `*`)

---

## Rutas API disponibles

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | /api/register | No | Registrar usuario |
| POST | /api/login | No | Iniciar sesión |
| POST | /api/logout | Sí | Cerrar sesión |
| GET | /api/user | Sí | Usuario actual |
| GET | /api/products | No | Listar productos |
| GET | /api/products/{id} | No | Ver producto |
| POST | /api/products | Sí | Crear producto |
| PUT | /api/products/{id} | Sí | Actualizar producto |
| DELETE | /api/products/{id} | Sí | Eliminar producto |
