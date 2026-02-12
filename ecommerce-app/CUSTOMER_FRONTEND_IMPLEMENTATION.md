# Implementación del Módulo de Cliente - Frontend Vue 3

## 📋 Resumen

Implementación completa del módulo de cliente en Vue 3 con Pinia, integrado con el backend Laravel + Sanctum. Incluye gestión de perfil, contraseñas, direcciones y órdenes.

---

## 🗂️ Estructura de Archivos

### Store (Pinia)
```
resources/js/stores/customer.js
```
- Estado centralizado para user, addresses, orders
- Manejo de loading states por operación
- Gestión de errores normalizada
- Métodos para todas las operaciones CRUD

### Co