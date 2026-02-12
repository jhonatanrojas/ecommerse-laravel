import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router/customer';

import CustomerDashboard from './Pages/Customer/CustomerDashboard.vue';

const app = createApp(CustomerDashboard);

const pinia = createPinia();
app.use(pinia);

app.use(router);

app.mount('#app');

