import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';

// Create a root component that uses router-view
const App = {
  template: '<router-view />'
};

// Create Vue app
const app = createApp(App);

// Use Pinia for state management
const pinia = createPinia();
app.use(pinia);

// Use Vue Router
app.use(router);

// Mount app
app.mount('#app');
