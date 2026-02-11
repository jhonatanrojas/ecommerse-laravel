import { useAuthStore } from '../stores/auth';

export async function requireAuth(redirectTo = '/') {
  const authStore = useAuthStore();
  
  // Verificar si el usuario está autenticado
  await authStore.checkAuth();
  
  if (!authStore.isAuthenticated) {
    // Guardar la URL de destino para redirigir después del login
    const currentUrl = window.location.pathname + window.location.search;
    window.location.href = `/register?redirect=${encodeURIComponent(currentUrl)}`;
    return false;
  }
  
  return true;
}

export async function requireGuest() {
  const authStore = useAuthStore();
  
  await authStore.checkAuth();
  
  if (authStore.isAuthenticated) {
    window.location.href = '/';
    return false;
  }
  
  return true;
}
