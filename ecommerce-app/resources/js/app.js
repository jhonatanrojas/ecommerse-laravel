import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import Sortable from 'sortablejs';
import 'flowbite';
import { initLazyLoad } from './plugins/lazyload';

// Plugins de Alpine
Alpine.plugin(collapse);
Alpine.plugin(focus);

// Hacer Sortable global
window.Sortable = Sortable;
window.Alpine = Alpine;

// Iniciar Alpine
Alpine.start();
initLazyLoad();
