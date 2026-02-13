import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import CategoriesPage from './Pages/CategoriesPage.vue';

const app = createApp(CategoriesPage);
app.use(createPinia());
initLazyLoad();
app.mount('#categories-app');
