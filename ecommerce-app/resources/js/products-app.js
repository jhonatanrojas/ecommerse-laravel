import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import ProductsPage from './Pages/ProductsPage.vue';

const app = createApp(ProductsPage);

app.use(createPinia());
initLazyLoad();
app.mount('#products-app');
