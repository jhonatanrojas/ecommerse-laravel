import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/home-app.js',
                'resources/js/products-app.js',
                'resources/js/product-detail-app.js',
                'resources/js/categories-app.js',
                'resources/js/category-products-app.js',
                'resources/js/auth-app.js',
                'resources/js/checkout-app.js',
                'resources/js/customer-app.js',
                'resources/js/marketplace-app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            'ziggy-js': 'vendor/tightenco/ziggy/dist/index.esm.js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('vue') || id.includes('pinia') || id.includes('vue-router')) {
                            return 'vendor-vue';
                        }
                        if (id.includes('@headlessui') || id.includes('@vueuse')) {
                            return 'vendor-ui';
                        }
                        if (id.includes('dayjs') || id.includes('vanilla-lazyload')) {
                            return 'vendor-utils';
                        }
                        return 'vendor';
                    }

                    if (id.includes('/Pages/Marketplace/')) return 'page-marketplace';
                    if (id.includes('/Pages/Customer/')) return 'page-customer';
                    if (id.includes('/Pages/Admin/')) return 'page-admin';
                },
            },
        },
    },
});
