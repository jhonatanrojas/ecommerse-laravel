import { defineStore } from 'pinia';
import axios from 'axios';

// Initialize CSRF token
let csrfInitialized = false;

async function initializeCsrf() {
  if (!csrfInitialized) {
    try {
      await axios.get('/sanctum/csrf-cookie');
      csrfInitialized = true;
    } catch (error) {
      console.error('Error initializing CSRF:', error);
    }
  }
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    authenticated: false,
    loading: false,
    errors: {},
    generalError: null,
  }),

  getters: {
    isAuthenticated: (state) => state.authenticated,
    currentUser: (state) => state.user,
    hasErrors: (state) => Object.keys(state.errors).length > 0,
  },

  actions: {
    async getCsrfCookie() {
      await initializeCsrf();
    },

    async register(userData) {
      this.loading = true;
      this.errors = {};
      this.generalError = null;

      try {
        await this.getCsrfCookie();
        
        const response = await axios.post('/register', {
          name: userData.name,
          email: userData.email,
          phone: userData.phone,
          password: userData.password,
          password_confirmation: userData.password_confirmation,
        });

        // Después del registro, obtener el usuario autenticado
        await this.fetchUser();
        
        return { success: true, data: response.data };
      } catch (error) {
        if (error.response?.status === 422) {
          // Errores de validación
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError = error.response?.data?.message || 'Error al registrar el usuario';
        }
        console.error('Error registering:', error);
        return { success: false, errors: this.errors, message: this.generalError };
      } finally {
        this.loading = false;
      }
    },

    async login(credentials) {
      this.loading = true;
      this.errors = {};
      this.generalError = null;

      try {
        await this.getCsrfCookie();
        
        const response = await axios.post('/login', {
          email: credentials.email,
          password: credentials.password,
          remember: credentials.remember || false,
        });

        await this.fetchUser();
        
        return { success: true, data: response.data };
      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.generalError = error.response?.data?.message || 'Credenciales incorrectas';
        }
        console.error('Error logging in:', error);
        return { success: false, errors: this.errors, message: this.generalError };
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      this.errors = {};
      this.generalError = null;

      try {
        await axios.post('/logout');
        
        this.user = null;
        this.authenticated = false;
        
        return { success: true };
      } catch (error) {
        this.generalError = error.response?.data?.message || 'Error al cerrar sesión';
        console.error('Error logging out:', error);
        return { success: false, message: this.generalError };
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/api/user');
        this.user = response.data;
        this.authenticated = true;
        return { success: true, user: this.user };
      } catch (error) {
        this.user = null;
        this.authenticated = false;
        console.error('Error fetching user:', error);
        return { success: false };
      }
    },

    async checkAuth() {
      // Verificar si el usuario está autenticado al cargar la app
      await this.fetchUser();
    },

    clearErrors() {
      this.errors = {};
      this.generalError = null;
    },

    getFieldError(field) {
      return this.errors[field]?.[0] || null;
    },
  },

  persist: {
    enabled: true,
    strategies: [
      {
        key: 'auth',
        storage: localStorage,
        paths: ['user', 'authenticated'],
      },
    ],
  },
});
