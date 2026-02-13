import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import router from './router/marketplace';
import MarketplaceApp from './Pages/Marketplace/MarketplaceApp.vue';

const app = createApp(MarketplaceApp);

app.use(createPinia());
app.use(router);
initLazyLoad();
app.mount('#marketplace-app');
