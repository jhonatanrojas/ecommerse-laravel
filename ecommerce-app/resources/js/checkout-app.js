import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';

// Import main checkout page
import CheckoutPage from './Pages/CheckoutPage.vue';

// Create Vue app
const app = createApp(CheckoutPage);

// Use Pinia for state management
const pinia = createPinia();
app.use(pinia);

// Use Vue Router
app.use(router);

// Mount app
app.mount('#app');
