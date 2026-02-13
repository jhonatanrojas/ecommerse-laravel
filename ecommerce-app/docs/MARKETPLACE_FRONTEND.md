# Marketplace Frontend (Vue 3 + Tailwind)

## Direccion visual
Diseño inspirado en experiencia de marketplace tipo Mercado Libre, con enfoque en conversion:
- Layout limpio, mucho espacio en blanco y alta legibilidad.
- Jerarquia fuerte de precio y CTA principal.
- Informacion secundaria organizada por bloques (envio, reputacion, soporte).
- Cards grandes con imagen prominente.
- Mobile-first con sticky search y filtros en drawer.

## Paletas propuestas
### Opcion A - Amarillo propio (seleccionada)
- Primario: `#F7C948`
- Primario fuerte: `#EAB308`
- Texto: `#2E2E2E`
- Fondo: `#F5F5F5`
- Acento: `#1A73E8`

### Opcion B - Azul profesional
- Primario: `#0057D9`
- Fondo suave: `#E8F0FE`
- Texto: `#1F1F1F`
- Acento exito: `#00A86B`

### Opcion C - Turquesa moderno
- Primario: `#00C2A8`
- Texto: `#2B2B2B`
- Fondo: `#FFFFFF`
- Acento calido: `#F9D65C`

### Justificacion de seleccion
Se selecciono **Opcion A** porque mantiene la energia comercial del amarillo (muy efectiva para conversion en marketplaces) sin copiar el tono de Mercado Libre, y se equilibra con azul de confianza para enlaces/estados interactivos.

## Rutas marketplace
- `/marketplace`
- `/marketplace/search?q=`
- `/marketplace/vendors/:slug`
- `/marketplace/products/:slug`
- `/messages/:orderUuid`

## Componentes clave
- `resources/js/Pages/Marketplace/MarketplaceApp.vue` (header sticky desktop + iconos + menu superior)
- `resources/js/components/layout/MobileHeader.vue` (header mobile sticky + SmartSearch + nav mobile)
- `resources/js/components/marketplace/MarketplaceHeader.vue` (breadcrumbs + resultados + sort + toggle grid/list)
- `resources/js/components/marketplace/FilterSidebar.vue` (filtros desktop)
- `resources/js/components/marketplace/MobileFilters.vue` (drawer filtros mobile)
- `resources/js/components/marketplace/MarketplaceProductCard.vue` (card vertical)
- `resources/js/components/marketplace/ProductCardMarketplace.vue` (card horizontal)
- `resources/js/components/marketplace/MarketplaceProductGallery.vue`
- `resources/js/components/marketplace/MarketplaceQuestions.vue`
- `resources/js/components/marketplace/MarketplaceReviews.vue`

## Comportamiento visual implementado
- Hover en tarjetas con elevacion y micro-movimiento.
- Skeleton loaders para grids y detalle.
- Sticky header y sticky barra de control.
- Sticky search en mobile dentro de header fijo.
- Drawer de filtros mobile con backdrop, gestos y CTA persistente.

## Store Pinia
`resources/js/stores/marketplace.js`
- `searchProducts(query, filters)`
- `fetchMarketplaceProducts(filters)`
- `fetchVendorProducts(slug)`
- `fetchVendor(slug)`
- `fetchProduct(slug)`
- `createOrder(productId, vendorId, buyerId)`
- `fetchQuestions(productId)`
- `submitQuestion(productId, text)`
- `fetchReviews(slug)`
- `submitReview(slug, payload)`

## API consumidas
- `GET /api/marketplace/products`
- `GET /api/marketplace/search?q=`
- `GET /api/search/autocomplete?q=`
- `GET /api/marketplace/vendors`
- `GET /api/marketplace/vendors/:slug`
- `GET /api/marketplace/vendors/:slug/products`
- `GET /api/marketplace/products/:slug`
- `GET /api/marketplace/products/:slug/reviews`
- `POST /api/marketplace/products/:slug/reviews`
- `POST /api/orders/direct`
- `GET /api/products/:id/questions`
- `POST /api/products/:id/questions`

## Tokens CSS marketplace
`resources/css/app.css`
- Variables `--mp-*` para tema visual.
- Utilidades: `.mp-shell`, `.mp-topbar`, `.mp-card`, `.mp-price`, `.mp-btn-primary`, `.mp-btn-secondary`, `.mp-skeleton`.
