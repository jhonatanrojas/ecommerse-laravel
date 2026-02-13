import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import RegisterPage from './Pages/Auth/RegisterPage.vue';
import LoginPage from './Pages/Auth/LoginPage.vue';

const pinia = createPinia();
const currentPath = window.location.pathname;
let component = null;

if (currentPath === '/register') {
  component = RegisterPage;
} else if (currentPath === '/login') {
  component = LoginPage;
}

if (component) {
  const app = createApp(component);
  app.use(pinia);
  initLazyLoad();
  app.mount('#app');
}
