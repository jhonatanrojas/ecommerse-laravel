# Dynamic Home Page Vue Components

This directory contains Vue components for the Dynamic Home Page Management System.

## Components Overview

### Main Component
- **Home.vue**: Main container component that fetches configuration from the API and dynamically renders section components

### Section Components
- **HeroSection.vue**: Hero banner with title, subtitle, background image/video, and CTA buttons
- **FeaturedProductsSection.vue**: Grid of featured products with images, prices, and ratings
- **FeaturedCategoriesSection.vue**: Grid of featured categories with images and product counts
- **BannersSection.vue**: Promotional banners in slider or grid layout with autoplay support
- **TestimonialsSection.vue**: Customer testimonials in carousel or grid layout
- **HtmlBlockSection.vue**: Custom HTML content block with sanitization

## Setup Instructions

### 1. Install Vue 3

```bash
npm install vue@^3.3.0
```

### 2. Update Vite Configuration

Add the Vue plugin to your `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/home-app.js', // Add this line
            ],
            refresh: true,
        }),
        vue(), // Add this line
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
```

### 3. Install Vite Vue Plugin

```bash
npm install @vitejs/plugin-vue --save-dev
```

### 4. Update Routes

Add a route to display the home page in `routes/web.php`:

```php
Route::get('/', function () {
    return view('home');
});
```

Or create a controller:

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

### 5. Build Assets

```bash
npm run dev
# or for production
npm run build
```

## API Endpoint

The Home component fetches data from `/api/home-configuration`. Ensure this endpoint is properly configured and returns data in the following format:

```json
{
  "data": [
    {
      "uuid": "...",
      "type": "hero",
      "title": "Welcome Section",
      "display_order": 1,
      "configuration": {...},
      "rendered_data": {
        "title": "Welcome to Our Store",
        "subtitle": "...",
        "background_image": "...",
        "cta_buttons": [...]
      }
    }
  ]
}
```

## Component Props

All section components receive a `section` prop with the following structure:

```javascript
{
  uuid: String,
  type: String,
  title: String,
  display_order: Number,
  configuration: Object,
  rendered_data: Object
}
```

## Customization

### Styling
- Components use Tailwind CSS classes
- Modify the classes in each component to match your design system
- Add custom styles in the `<style scoped>` sections

### Layout
- Grid columns are configurable via the `rendered_data.columns` property
- Layouts can be switched between 'grid', 'slider', and 'carousel' depending on the section type

### Sanitization
The HtmlBlockSection component includes basic HTML sanitization. For production use, consider installing and using DOMPurify:

```bash
npm install dompurify
```

Then update the component:

```javascript
import DOMPurify from 'dompurify';

computed: {
  sanitizedHtml() {
    return DOMPurify.sanitize(this.renderedData.html_content || '');
  }
}
```

## Browser Support

These components are built with Vue 3 and require modern browser support:
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

## Troubleshooting

### Components not rendering
1. Check browser console for errors
2. Verify the API endpoint is accessible and returning data
3. Ensure Vue is properly installed and configured
4. Check that Vite is building the assets correctly

### Styles not applying
1. Ensure Tailwind CSS is properly configured
2. Check that the CSS is being imported in your Vite entry point
3. Verify that the Tailwind directives are in your CSS file

### API errors
1. Check that the backend API is running
2. Verify CORS settings if API is on a different domain
3. Check network tab in browser dev tools for response details
