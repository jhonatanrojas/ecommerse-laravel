import { createApp } from 'vue';
import Home from './components/Home.vue';

// Create and mount the Vue app for the home page
const app = createApp(Home);
app.mount('#home-app');
