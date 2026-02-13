import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import router from './router/customer';
import CustomerApp from './Pages/Customer/CustomerApp.vue';

const app = createApp(CustomerApp);

const pinia = createPinia();
app.use(pinia);
app.use(router);

initLazyLoad();
app.mount('#app');
