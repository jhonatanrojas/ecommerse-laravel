import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import CategoryProductsPage from './Pages/CategoryProductsPage.vue';

const app = createApp(CategoryProductsPage);
app.use(createPinia());
app.mount('#category-products-app');
