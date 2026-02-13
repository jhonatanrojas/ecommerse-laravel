import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import CategoryProductsPage from './Pages/CategoryProductsPage.vue';

const app = createApp(CategoryProductsPage);
app.use(createPinia());
initLazyLoad();
app.mount('#category-products-app');
