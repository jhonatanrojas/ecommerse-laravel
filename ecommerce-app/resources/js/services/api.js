import axios from 'axios';

// Create axios instance with default config
const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// CSRF token initialization
let csrfInitialized = false;

export async function initializeCsrf() {
  if (!csrfInitialized) {
    try {
      await axios.get('/sanctum/csrf-cookie');
      csrfInitialized = true;
    } catch (error) {
      console.error('Error initializing CSRF:', error);
      throw error;
    }
  }
}

// Request interceptor
api.interceptors.request.use(
  async (config) => {
    // Ensure CSRF token is initialized before requests
    await initializeCsrf();
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Handle common errors
    if (error.response) {
      const { status, data } = error.response;
      
      // Handle 401 Unauthorized
      if (status === 401) {
        console.error('Unauthorized access - redirecting to login');
        // You can add redirect logic here if needed
      }
      
      // Handle 419 CSRF token mismatch
      if (status === 419) {
        console.error('CSRF token mismatch - reinitializing');
        csrfInitialized = false;
        // Retry the request after reinitializing CSRF
        return initializeCsrf().then(() => api.request(error.config));
      }
      
      // Handle validation errors (422)
      if (status === 422 && data.errors) {
        error.validationErrors = data.errors;
      }
    }
    
    return Promise.reject(error);
  }
);

export default api;
