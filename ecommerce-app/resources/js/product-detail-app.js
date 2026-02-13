import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import ProductDetailPage from './Pages/ProductDetailPage.vue';

const app = createApp(ProductDetailPage);

app.use(createPinia());
initLazyLoad();
app.mount('#product-detail-app');
