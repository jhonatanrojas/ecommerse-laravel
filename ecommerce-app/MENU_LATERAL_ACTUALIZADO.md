# Menú Lateral Admin - Actualización

## Cambio Realizado

Se ha agregado la opción "Estatus de Envíos" al menú lateral del panel administrativo en la sección de Ajustes.

## Archivo Modificado

- ✅ `resources/views/layouts/admin.blade.php`

## Ubicación en el Menú

```
Panel Admin
├── Dashboard
├── Categorías
├── Productos
├── Órdenes
├── Usuarios
└── Ajustes ▼
    ├── Generales
    ├── Gestión de Secciones
    ├── Menús
    ├── Métodos de Pago
    ├── Estatus de Órdenes
    └── Estatus de Envíos  ← NUEVO
```

## Características

### 1. Enlace Activo
El enlace se resalta cuando estás en cualquier ruta relacionada con shipping-statuses:
- `/admin/shipping-statuses` (índice)
- `/admin/shipping-statuses/create` (crear)
- `/admin/shipping-statuses/{id}/edit` (editar)

### 2. Dropdown Automático
El menú de "Ajustes" se mantiene abierto automáticamente cuando:
- Estás en cualquier ruta de `admin.shipping-statuses.*`
- Estás en otras rutas de ajustes (settings, home-sections, menus, payment-methods, order-statuses)

### 3. Estilos Consistentes
- Usa los mismos estilos que los demás elementos del menú
- Resaltado en gris cuando está activo
- Hover effect en todos los estados
- Compatible con modo oscuro

## Código Agregado

### Enlace en el Menú
```blade
<li>
    <a href="{{ route('admin.shipping-statuses.index') }}" 
       class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs('admin.shipping-statuses.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
        Estatus de Envíos
    </a>
</li>
```

### Actualización del Botón de Ajustes
Se agregó `request()->routeIs('admin.shipping-statuses.*')` a la condición para resaltar el botón de Ajustes:

```blade
{{ request()->routeIs('admin.settings.*') || 
   request()->routeIs('admin.home-sections.*') || 
   request()->routeIs('admin.menus.*') || 
   request()->routeIs('admin.payment-methods.*') || 
   request()->routeIs('admin.order-statuses.*') || 
   request()->routeIs('admin.shipping-statuses.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}
```

### Actualización del Script JavaScript
Se agregó la verificación de rutas de shipping-statuses para mantener el dropdown abierto:

```javascript
const isSettingsRoute = {{ 
    request()->routeIs('admin.settings.*') || 
    request()->routeIs('admin.home-sections.*') || 
    request()->routeIs('admin.menus.*') || 
    request()->routeIs('admin.payment-methods.*') || 
    request()->routeIs('admin.order-statuses.*') || 
    request()->routeIs('admin.shipping-statuses.*') ? 'true' : 'false' 
}};
```

## Acceso

### Desde el Panel Admin
1. Inicia sesión en el panel admin
2. En el menú lateral, haz clic en "Ajustes"
3. Selecciona "Estatus de Envíos"
4. Serás redirigido a `/admin/shipping-statuses`

### URL Directa
```
http://tu-dominio.com/admin/shipping-statuses
```

## Rutas Relacionadas

Todas estas rutas están disponibles desde el panel de Estatus de Envíos:

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/admin/shipping-statuses` | Listar todos los estatus |
| POST | `/admin/shipping-statuses` | Crear nuevo estatus |
| PUT | `/admin/shipping-statuses/{id}` | Actualizar estatus |
| PATCH | `/admin/shipping-statuses/{id}/toggle` | Activar/Desactivar |
| PATCH | `/admin/shipping-statuses/{id}/default` | Marcar como default |
| DELETE | `/admin/shipping-statuses/{id}` | Eliminar estatus |

## Permisos

El acceso al panel de Estatus de Envíos requiere:
- ✅ Autenticación con guard `admin`
- ✅ Middleware `auth:admin`
- ✅ Middleware `verified`

## Capturas de Pantalla (Descripción)

### Menú Colapsado
```
Ajustes >
```

### Menú Expandido
```
Ajustes ▼
  Generales
  Gestión de Secciones
  Menús
  Métodos de Pago
  Estatus de Órdenes
  Estatus de Envíos  ← Nuevo
```

### Estado Activo
Cuando estás en `/admin/shipping-statuses`, el elemento "Estatus de Envíos" aparece con fondo gris claro (o gris oscuro en modo oscuro).

## Compatibilidad

- ✅ Compatible con Flowbite 2.5.1
- ✅ Compatible con Tailwind CSS
- ✅ Responsive (funciona en móviles)
- ✅ Modo oscuro soportado
- ✅ Accesibilidad (ARIA labels)

## Testing

### Verificar el Enlace
1. Accede al panel admin
2. Haz clic en "Ajustes"
3. Verifica que aparece "Estatus de Envíos"
4. Haz clic en "Estatus de Envíos"
5. Deberías ver la página de gestión de estatus de envíos

### Verificar Estado Activo
1. Navega a `/admin/shipping-statuses`
2. El menú "Ajustes" debe estar expandido automáticamente
3. "Estatus de Envíos" debe tener fondo gris
4. El botón "Ajustes" también debe tener fondo gris

### Verificar en Móvil
1. Abre el panel admin en un dispositivo móvil
2. Toca el ícono de hamburguesa para abrir el sidebar
3. Toca "Ajustes"
4. Verifica que "Estatus de Envíos" aparece en la lista

## Orden de los Elementos

Los elementos en el menú de Ajustes están ordenados lógicamente:

1. **Generales** - Configuración general de la tienda
2. **Gestión de Secciones** - Secciones de la página principal
3. **Menús** - Menús de navegación
4. **Métodos de Pago** - Configuración de pagos
5. **Estatus de Órdenes** - Estados del flujo de órdenes
6. **Estatus de Envíos** - Estados del flujo de envíos ← Nuevo

## Próximos Pasos

Después de agregar el enlace al menú:

1. ✅ Verificar que el enlace funciona correctamente
2. ✅ Probar la navegación entre secciones
3. ✅ Verificar que el estado activo se muestra correctamente
4. 🔄 Agregar permisos específicos si es necesario
5. 🔄 Agregar contador de estatus (opcional)

## Notas Adicionales

### Agregar Contador (Opcional)
Si deseas mostrar el número de estatus de envío en el menú:

```blade
<a href="{{ route('admin.shipping-statuses.index') }}" class="...">
    Estatus de Envíos
    <span class="inline-flex items-center justify-center w-5 h-5 ms-2 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full">
        {{ \App\Models\ShippingStatus::count() }}
    </span>
</a>
```

### Agregar Ícono (Opcional)
Si deseas agregar un ícono específico:

```blade
<a href="{{ route('admin.shipping-statuses.index') }}" class="...">
    <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
        <!-- SVG del ícono de envío -->
    </svg>
    Estatus de Envíos
</a>
```

## Troubleshooting

### El enlace no aparece
**Solución:** Limpia la caché de vistas:
```bash
php artisan view:clear
```

### El dropdown no se abre automáticamente
**Solución:** Verifica que Flowbite JS esté cargado correctamente. Revisa la consola del navegador.

### El estado activo no se muestra
**Solución:** Verifica que la ruta actual coincida con el patrón `admin.shipping-statuses.*`

## Resumen

✅ Enlace agregado al menú de Ajustes  
✅ Estado activo configurado  
✅ Dropdown automático funcionando  
✅ Compatible con modo oscuro  
✅ Responsive y accesible  
✅ Listo para usar  

El menú lateral del panel admin ahora incluye acceso directo a la gestión de Estatus de Envíos desde la sección de Ajustes.
