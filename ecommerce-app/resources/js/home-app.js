import { createApp } from 'vue';
import { createPinia } from 'pinia';
import HomePage from './Pages/HomePage.vue';
import CartDrawer from './components/cart/CartDrawer.vue';
import CartButton from './components/cart/CartButton.vue';
import AddToCartButton from './components/cart/AddToCartButton.vue';
import CartToast from './components/cart/CartToast.vue';

// Create Pinia instance
const pinia = createPinia();

// Create and mount the Vue app for the home page
const app = createApp(HomePage);

// Use Pinia
app.use(pinia);

// Register global components
app.component('CartDrawer', CartDrawer);
app.component('CartButton', CartButton);
app.component('AddToCartButton', AddToCartButton);
app.component('CartToast', CartToast);

app.mount('#home-app');
