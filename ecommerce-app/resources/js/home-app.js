import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { initLazyLoad } from './plugins/lazyload';
import HomePage from './Pages/HomePage.vue';
import CartDrawer from './components/cart/CartDrawer.vue';
import CartButton from './components/cart/CartButton.vue';
import AddToCartButton from './components/cart/AddToCartButton.vue';
import CartToast from './components/cart/CartToast.vue';

const pinia = createPinia();
const app = createApp(HomePage);

app.use(pinia);
app.component('CartDrawer', CartDrawer);
app.component('CartButton', CartButton);
app.component('AddToCartButton', AddToCartButton);
app.component('CartToast', CartToast);

initLazyLoad();
app.mount('#home-app');
