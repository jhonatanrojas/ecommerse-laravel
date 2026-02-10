# Dynamic Home Page Frontend Setup Guide

This guide explains how to set up and integrate the Vue.js components for the Dynamic Home Page Management System.

## Overview

The frontend consists of Vue 3 components that fetch configuration from the API endpoint and dynamically render home page sections. The components are located in `resources/js/components/`.

## Prerequisites

- Node.js 16+ and npm
- Laravel application with Vite
- Tailwind CSS configured

## Installation Steps

### 1. Install Vue 3 and Required Dependencies

```bash
npm install vue@^3.3.0 @vitejs/plugin-vue --save-dev
```

### 2. Update Vite Configuration

Update your `vite.config.js` file:

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
                'resources/js/home-app.js', // Add this for the home page
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
        },
    },
});
```

### 3. Update Package.json Scripts (Optional)

Your existing scripts should work, but verify they're present:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  }
}
```

### 4. Configure Routes

Update `routes/web.php` to serve the home page:

```php
Route::get('/', function () {
    return view('home');
})->name('home');
```

Or create a dedicated controller:

```php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
}

// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

### 5. Build Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Component Structure

```
resources/js/components/
├── Home.vue                          # Main container component
└── sections/
    ├── HeroSection.vue              # Hero banner section
    ├── FeaturedProductsSection.vue  # Featured products grid
    ├── FeaturedCategoriesSection.vue # Featured categories grid
    ├── BannersSection.vue           # Promotional banners
    ├── TestimonialsSection.vue      # Customer testimonials
    └── HtmlBlockSection.vue         # Custom HTML content
```

## API Integration

The components fetch data from `/api/home-configuration`. Ensure:

1. The API endpoint is registered in `routes/api.php`
2. The endpoint returns data in the correct format
3. CORS is configured if needed

Expected API response format:

```json
{
  "data": [
    {
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "type": "hero",
      "title": "Welcome Section",
      "display_order": 1,
      "configuration": {
        "title": "Welcome to Our Store",
        "subtitle": "Discover amazing products"
      },
      "rendered_data": {
        "title": "Welcome to Our Store",
        "subtitle": "Discover amazing products",
        "background_image": "/images/hero-bg.jpg",
        "cta_buttons": [
          {
            "text": "Shop Now",
            "url": "/shop",
            "style": "primary"
          }
        ]
      }
    }
  ]
}
```

## Testing the Setup

1. Start the development server:
   ```bash
   npm run dev
   ```

2. Start Laravel:
   ```bash
   php artisan serve
   ```

3. Visit `http://localhost:8000` in your browser

4. Open browser console to check for any errors

5. Verify that the API endpoint is being called and returning data

## Customization

### Styling

All components use Tailwind CSS. To customize:

1. Modify the Tailwind classes in each component
2. Add custom styles in the `<style scoped>` sections
3. Update your `tailwind.config.js` if needed

### Adding New Section Types

1. Create a new component in `resources/js/components/sections/`
2. Import it in `Home.vue`
3. Add it to the `components` object
4. Add the mapping in the `getSectionComponent` method

Example:

```javascript
// In Home.vue
import NewSection from './sections/NewSection.vue';

export default {
  components: {
    // ... existing components
    NewSection,
  },
  methods: {
    getSectionComponent(type) {
      const componentMap = {
        // ... existing mappings
        new_section: 'NewSection',
      };
      return componentMap[type] || null;
    },
  },
};
```

## Production Considerations

### 1. HTML Sanitization

The HtmlBlockSection includes basic sanitization. For production, install DOMPurify:

```bash
npm install dompurify
npm install --save-dev @types/dompurify
```

Update `HtmlBlockSection.vue`:

```javascript
import DOMPurify from 'dompurify';

computed: {
  sanitizedHtml() {
    return DOMPurify.sanitize(this.renderedData.html_content || '', {
      ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'a', 'img'],
      ALLOWED_ATTR: ['href', 'src', 'alt', 'title', 'class']
    });
  }
}
```

### 2. Image Optimization

Consider using lazy loading for images:

```vue
<img :src="image" loading="lazy" alt="..." />
```

### 3. Performance

- Enable caching on the API endpoint (already implemented in the backend)
- Use production build: `npm run build`
- Consider implementing a service worker for offline support

### 4. SEO

For better SEO, consider:
- Server-side rendering (SSR) with Inertia.js
- Pre-rendering static pages
- Adding meta tags dynamically

## Troubleshooting

### Issue: Components not rendering

**Solution:**
1. Check browser console for errors
2. Verify Vue is installed: `npm list vue`
3. Ensure Vite is running: `npm run dev`
4. Check that the mount point exists: `<div id="home-app"></div>`

### Issue: API errors

**Solution:**
1. Verify the API endpoint exists and is accessible
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test the endpoint directly: `curl http://localhost:8000/api/home-configuration`
4. Check CORS configuration if API is on different domain

### Issue: Styles not applying

**Solution:**
1. Ensure Tailwind CSS is configured
2. Verify `resources/css/app.css` includes Tailwind directives
3. Check that CSS is being imported in Vite entry point
4. Clear browser cache and rebuild: `npm run build`

### Issue: Hot reload not working

**Solution:**
1. Restart Vite: `npm run dev`
2. Check Vite configuration
3. Ensure file watchers are not at limit (Linux): `echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf && sudo sysctl -p`

## Alternative: Using with Inertia.js

If you want to use Inertia.js instead of standalone Vue:

1. Install Inertia:
   ```bash
   composer require inertiajs/inertia-laravel
   npm install @inertiajs/vue3
   ```

2. Follow Inertia setup guide: https://inertiajs.com/server-side-setup

3. Move components to `resources/js/Pages/`

4. Update controller to use Inertia:
   ```php
   use Inertia\Inertia;
   
   public function index()
   {
       return Inertia::render('Home');
   }
   ```

## Support

For issues or questions:
1. Check the component README: `resources/js/components/README.md`
2. Review the design document: `.kiro/specs/dynamic-home-page-management/design.md`
3. Check Laravel logs and browser console for errors
