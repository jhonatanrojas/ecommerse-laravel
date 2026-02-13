import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import router from './router';

const App = {
  template: '<router-view />',
};

const app = createApp(App);

const pinia = createPinia();
app.use(pinia);
app.use(router);

initLazyLoad();
app.mount('#app');
