# Módulo de Registro de Clientes - Vue 3 + Laravel Sanctum

## 📋 Descripción

Módulo completo de autenticación para el frontend del ecommerce usando Vue 3, Pinia y Laravel Sanctum con autenticación basada en cookies.

## 🏗️ Estructura del Proyecto

```
resources/js/
├── stores/
│   └── auth.js                          # Store de autenticación (Pinia)
├── Pages/
│   └── Auth/
│       ├── RegisterPage.vue             # Vista de registro
│       └── LoginPage.vue                # Vista de login
├── components/
│   ├── forms/
│   │   ├── InputText.vue                # Input de texto reutilizable
│   │   ├── InputPassword.vue            # Input de contraseña con toggle
│   │   ├── FormError.vue                # Componente de errores
│   │   └── SubmitButton.vue             # Botón de submit con loading
│   └── ui/
│       ├── Toast.vue                    # Componente de notificación
│       └── ToastContainer.vue           # Contenedor de toasts
├── composables/
│   └── useToast.js                      # Composable para toasts
├── middleware/
│   └── auth.js                          # Middleware de autenticación
├── auth-app.js                          # Entry point para páginas de auth
└── bootstrap.js                         # Configuración de axios

resources/views/auth/
├── register.blade.php                   # Vista Blade para registro
└── login.blade.php                      # Vista Blade para login
```

## 🚀 Características Implementadas

### AuthStore (Pinia)

**Estado:**
- `user`: Datos del usuario autenticado
- `authenticated`: Estado de autenticación
- `loading`: Estado de carga
- `errors`: Errores de validación del backend
- `generalError`: Error general

**Métodos:**
- `getCsrfCookie()`: Obtiene el token CSRF de Sanctum
- `register(userData)`: Registra un nuevo usuario
- `login(credentials)`: Inicia sesión
- `logout()`: Cierra sesión
- `fetchUser()`: Obtiene datos del usuario autenticado
- `checkAuth()`: Verifica si el usuario está autenticado
- `clearErrors()`: Limpia errores
- `getFieldError(field)`: Obtiene error de un campo específico

**Persistencia:**
- Los datos del usuario se persisten en `localStorage`
- Se mantiene el estado entre recargas de página

### Componentes Reutilizables

#### InputText.vue
```vue
<InputText
  v-model="form.email"
  name="email"
  type="email"
  label="Correo electrónico"
  placeholder="tu@email.com"
  :required="true"
  :error="authStore.getFieldError('email')"
  autocomplete="email"
/>
```

#### InputPassword.vue
```vue
<InputPassword
  v-model="form.password"
  name="password"
  label="Contraseña"
  :required="true"
  :error="authStore.getFieldError('password')"
  autocomplete="new-password"
/>
```

#### FormError.vue
```vue
<FormError
  :message="authStore.generalError"
  :errors="authStore.errors"
  :dismissible="true"
  @dismiss="authStore.clearErrors()"
/>
```

#### SubmitButton.vue
```vue
<SubmitButton
  text="Crear cuenta"
  loading-text="Creando cuenta..."
  :loading="authStore.loading"
  :disabled="!isFormValid"
/>
```

### Vistas

#### RegisterPage.vue
- Formulario de registro con validaciones
- Campos: nombre, email, teléfono, contraseña, confirmar contraseña
- Validación en frontend antes de enviar
- Muestra errores del backend
- Checkbox de términos y condiciones
- Enlace a página de login
- Redirección automática después del registro

#### LoginPage.vue
- Formulario de login
- Campos: email, contraseña
- Checkbox "Recordarme"
- Enlace a recuperación de contraseña
- Enlace a página de registro
- Redirección automática después del login

## 🔧 Configuración

### 1. Axios (bootstrap.js)

Ya está configurado con:
```javascript
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;
```

### 2. Vite (vite.config.js)

Ya incluye el entry point `auth-app.js`:
```javascript
input: [
  'resources/css/app.css',
  'resources/js/app.js',
  'resources/js/home-app.js',
  'resources/js/auth-app.js',
],
```

### 3. Rutas Laravel

Las rutas ya están configuradas en `routes/auth.php`:
- `GET /register` - Vista de registro
- `POST /register` - Procesar registro
- `GET /login` - Vista de login
- `POST /login` - Procesar login
- `POST /logout` - Cerrar sesión
- `GET /api/user` - Obtener usuario autenticado

## 📝 Uso

### Integración con Checkout

Para proteger rutas que requieren autenticación (como checkout):

```javascript
import { requireAuth } from '../middleware/auth';

// En tu componente de checkout
onMounted(async () => {
  const isAuthenticated = await requireAuth();
  if (!isAuthenticated) {
    // El usuario será redirigido a /register
    return;
  }
  
  // Continuar con el checkout
  await loadCheckoutData();
});
```

### Sincronización del Carrito

Después del registro/login, el carrito se sincroniza automáticamente:

```javascript
const result = await authStore.register(userData);

if (result.success) {
  // Sincronizar carrito del invitado con el usuario autenticado
  await cartStore.fetchCart();
  
  // Redirigir
  const redirectTo = new URLSearchParams(window.location.search).get('redirect') || '/';
  window.location.href = redirectTo;
}
```

### Usar Toasts (Opcional)

```javascript
import { useToast } from '../composables/useToast';

const toast = useToast();

// Mostrar notificación de éxito
toast.success('¡Cuenta creada!', 'Bienvenido a nuestra tienda');

// Mostrar error
toast.error('Error', 'No se pudo crear la cuenta');
```

## 🎨 Validaciones Frontend

### Registro
- Nombre: requerido, no vacío
- Email: requerido, formato válido
- Teléfono: opcional
- Contraseña: requerida, mínimo 8 caracteres
- Confirmar contraseña: debe coincidir con contraseña
- Términos: debe aceptar

### Login
- Email: requerido, no vacío
- Contraseña: requerida, mínimo 6 caracteres

## 🔒 Seguridad

- CSRF token automático con Sanctum
- Cookies HTTP-only para sesiones
- Validación en backend y frontend
- Sanitización de inputs
- Protección contra XSS

## 🎯 Flujo de Usuario

### Registro
1. Usuario accede a `/register`
2. Completa el formulario
3. Frontend valida los datos
4. Se envía POST a `/register`
5. Backend valida y crea el usuario
6. Usuario se autentica automáticamente
7. Se sincroniza el carrito
8. Redirección al home o página anterior

### Login
1. Usuario accede a `/login`
2. Ingresa credenciales
3. Frontend valida los datos
4. Se envía POST a `/login`
5. Backend valida credenciales
6. Usuario se autentica
7. Se sincroniza el carrito
8. Redirección al home o página anterior

### Checkout sin autenticación
1. Usuario intenta acceder al checkout
2. Middleware detecta que no está autenticado
3. Redirección a `/register?redirect=/checkout`
4. Usuario se registra/inicia sesión
5. Redirección automática al checkout

## 📱 Responsive Design

Todos los componentes están diseñados con Tailwind CSS y son completamente responsive:
- Mobile-first approach
- Breakpoints: sm, md, lg
- Touch-friendly en dispositivos móviles

## 🎨 Personalización

### Colores
Los componentes usan clases de Tailwind. Para cambiar el color principal:
- Buscar: `blue-600`, `blue-700`, `blue-500`
- Reemplazar con tu color: `indigo-600`, `purple-600`, etc.

### Textos
Todos los textos están en español y pueden modificarse directamente en los componentes.

## 🐛 Manejo de Errores

### Errores de Validación (422)
```javascript
{
  "errors": {
    "email": ["El correo ya está registrado"],
    "password": ["La contraseña debe tener al menos 8 caracteres"]
  }
}
```

### Errores Generales
```javascript
{
  "message": "Error al procesar la solicitud"
}
```

## 🚀 Próximos Pasos

1. **Compilar assets:**
   ```bash
   npm run dev
   # o para producción
   npm run build
   ```

2. **Probar el registro:**
   - Acceder a `http://localhost/register`
   - Completar el formulario
   - Verificar redirección

3. **Integrar con checkout:**
   - Agregar middleware `requireAuth` en la página de checkout
   - Probar flujo completo

4. **Personalizar:**
   - Ajustar colores según tu marca
   - Modificar textos si es necesario
   - Agregar campos adicionales si lo requieres

## 📚 Recursos Adicionales

- [Laravel Sanctum Docs](https://laravel.com/docs/sanctum)
- [Vue 3 Docs](https://vuejs.org/)
- [Pinia Docs](https://pinia.vuejs.org/)
- [Tailwind CSS Docs](https://tailwindcss.com/)

## ✅ Checklist de Implementación

- [x] AuthStore con Pinia
- [x] Componentes de formulario reutilizables
- [x] Vista de registro
- [x] Vista de login
- [x] Validaciones frontend
- [x] Manejo de errores backend
- [x] Integración con Sanctum
- [x] Sincronización de carrito
- [x] Middleware de autenticación
- [x] Sistema de toasts (opcional)
- [x] Responsive design
- [x] Persistencia de sesión

## 🎉 ¡Listo para usar!

El módulo está completamente funcional y listo para integrarse con tu ecommerce.
