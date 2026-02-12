import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import CategoriesPage from './Pages/CategoriesPage.vue';

const app = createApp(CategoriesPage);
app.use(createPinia());
app.mount('#categories-app');
