import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import ProductsPage from './Pages/ProductsPage.vue';

const app = createApp(ProductsPage);

app.use(createPinia());
app.mount('#products-app');
