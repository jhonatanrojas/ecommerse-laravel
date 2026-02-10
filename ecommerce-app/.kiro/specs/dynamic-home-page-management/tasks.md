# Implementation Plan: Dynamic Home Page Management System

## Overview

This implementation plan breaks down the Dynamic Home Page Management System into discrete coding tasks. The system will be built incrementally, starting with database foundations, then models and repositories, followed by services and renderers, controllers, and finally frontend components. Each phase includes testing tasks to validate functionality early.

## Tasks

- [x] 1. Set up database schema and migrations
  - [x] 1.1 Create home_sections table migration
    - Create migration file with columns: id, uuid, type (enum), title, is_active, display_order, configuration (JSON), timestamps, soft deletes
    - Add indexes on uuid, display_order, is_active, deleted_at
    - _Requirements: 1.1, 1.3, 1.4, 1.5_
  
  - [x] 1.2 Create home_section_items table migration
    - Create migration file with columns: id, home_section_id (foreign key), itemable_type, itemable_id, display_order, configuration (JSON), timestamps
    - Add composite index on (home_section_id, display_order)
    - Add composite index on (itemable_type, itemable_id)
    - Add foreign key constraint with CASCADE on delete
    - _Requirements: 1.2, 1.6, 1.7, 1.8_

- [x] 2. Implement Eloquent models with relationships
  - [x] 2.1 Create HomeSection model
    - Implement model with HasUuids and SoftDeletes traits
    - Define fillable attributes: type, title, is_active, display_order, configuration
    - Add casts for is_active (boolean), configuration (array), display_order (integer)
    - Define hasMany relationship to HomeSectionItem
    - Add active() and ordered() query scopes
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.9_
  
  - [x] 2.2 Create HomeSectionItem model
    - Implement model with fillable attributes: home_section_id, itemable_type, itemable_id, display_order, configuration
    - Add casts for configuration (array), display_order (integer)
    - Define belongsTo relationship to HomeSection
    - Define morphTo relationship named itemable
    - _Requirements: 2.6, 2.7, 2.8, 2.10_

- [x] 3. Implement model observers for cache invalidation
  - [x] 3.1 Create HomeSectionObserver
    - Implement observer with created(), updated(), deleted() methods
    - Each method should invalidate cache tagged with 'home_sections'
    - Register observer in AppServiceProvider or EventServiceProvider
    - _Requirements: 3.1, 3.2, 3.3_
  
  - [x] 3.2 Create HomeSectionItemObserver
    - Implement observer with created(), updated(), deleted() methods
    - Each method should invalidate cache tagged with 'home_sections'
    - Register observer in AppServiceProvider or EventServiceProvider
    - _Requirements: 3.4, 3.5, 3.6_

- [x] 4. Create repository layer with interface
  - [x] 4.1 Define HomeSectionRepositoryInterface
    - Create interface with methods: getAllActive(), getById($id), create($data), update($id, $data), delete($id), reorder($sectionIds)
    - _Requirements: 4.1_
  
  - [x] 4.2 Implement HomeSectionRepository
    - Implement getAllActive() with active scope, ordered scope, eager loading items.itemable, and caching (3600s, tag 'home_sections')
    - Implement getById() with eager loading items.itemable
    - Implement create() to create new HomeSection
    - Implement update() to update existing HomeSection
    - Implement delete() to soft delete HomeSection
    - Implement reorder() to update display_order for multiple sections
    - _Requirements: 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 4.9, 4.10_

- [x] 5. Implement service layer for business logic
  - [x] 5.1 Create HomeConfigurationService
    - Inject HomeSectionRepositoryInterface and HomeSectionRendererService
    - Implement getCompleteConfiguration() to retrieve active sections and render each with HomeSectionRendererService
    - Implement toggleSectionStatus($sectionId, $isActive) to update is_active field
    - Implement reorderSections($sectionIds) to delegate to repository's reorder method
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8_

- [x] 6. Implement Strategy Pattern for section renderers
  - [x] 6.1 Create SectionRendererInterface
    - Define interface with render(HomeSection $section): array method
    - _Requirements: 6.1_
  
  - [x] 6.2 Create HeroRenderer
    - Implement SectionRendererInterface
    - Extract title, subtitle, background_image, background_video, cta_buttons, overlay_opacity from configuration
    - Return structured array with hero data
    - _Requirements: 6.2_
  
  - [x] 6.3 Create FeaturedProductsRenderer
    - Implement SectionRendererInterface
    - Retrieve section items with eager loaded itemable.images and itemable.category
    - Map items to product data (id, name, slug, price, sale_price, image, category, rating)
    - Filter out null itemables (deleted products)
    - Respect limit from configuration
    - Return structured array with layout, columns, show_price, show_rating, products
    - _Requirements: 6.3, 7.1, 7.2, 7.3, 7.4, 7.5_
  
  - [x] 6.4 Create FeaturedCategoriesRenderer
    - Implement SectionRendererInterface
    - Retrieve section items with eager loaded itemable.image and itemable.products
    - Map items to category data (id, name, slug, image, product_count, description)
    - Filter out null itemables (deleted categories)
    - Respect limit from configuration
    - Return structured array with layout, columns, show_product_count, categories
    - _Requirements: 6.4, 8.1, 8.2, 8.3, 8.4, 8.5_
  
  - [x] 6.5 Create BannersRenderer
    - Implement SectionRendererInterface
    - Extract layout, autoplay, autoplay_speed, banners from configuration
    - Return structured array with banner data
    - _Requirements: 6.5_
  
  - [x] 6.6 Create TestimonialsRenderer
    - Implement SectionRendererInterface
    - Extract layout, show_rating, show_avatar, testimonials from configuration
    - Return structured array with testimonial data
    - _Requirements: 6.6_
  
  - [x] 6.7 Create HtmlBlockRenderer
    - Implement SectionRendererInterface
    - Extract html_content and css_classes from configuration
    - Return structured array with HTML block data
    - _Requirements: 6.7_
  
  - [x] 6.8 Create HomeSectionRendererService
    - Inject all renderer implementations via constructor
    - Register renderers in a map keyed by section type
    - Implement render(HomeSection $section) to select appropriate renderer and invoke it
    - Throw InvalidSectionTypeException for unsupported types
    - _Requirements: 6.8, 6.9, 6.10, 6.11_

- [x] 7. Create custom exception classes
  - [x] 7.1 Create InvalidSectionTypeException
    - Extend base Exception class
    - Used when renderer encounters unsupported section type
    - _Requirements: 6.11, 18.1_

- [ ] 8. Checkpoint - Ensure core services work
  - Run migrations to create tables
  - Test that models can be created and relationships work
  - Test that repository methods retrieve and cache data correctly
  - Test that renderers produce expected output structures
  - Ensure all tests pass, ask the user if questions arise

- [x] 9. Implement form request validation
  - [x] 9.1 Create StoreHomeSectionRequest
    - Validate type: required, in allowed section types (hero, featured_products, featured_categories, banners, testimonials, html_block)
    - Validate title: required, string, max 255 characters
    - Validate is_active: boolean
    - Validate display_order: integer, min 0
    - Validate configuration: required, array
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_
  
  - [x] 9.2 Create UpdateHomeSectionRequest
    - Validate type: required, in allowed section types
    - Validate title: required, string, max 255 characters
    - Validate is_active: boolean
    - Validate display_order: integer, min 0
    - Validate configuration: required, array
    - _Requirements: 12.6, 12.7, 12.8, 12.9, 12.10_

- [x] 10. Implement admin CRUD controller
  - [x] 10.1 Create Admin/HomeSectionController
    - Inject HomeSectionRepositoryInterface and HomeConfigurationService
    - Implement index() to display all sections (including soft-deleted) ordered by display_order, render with Inertia
    - Implement create() to display section creation form with Inertia
    - Implement store(StoreHomeSectionRequest) to create section via repository, redirect with success message
    - Implement edit($id) to display section edit form with Inertia
    - Implement update(UpdateHomeSectionRequest, $id) to update section via repository, redirect with success message
    - Implement destroy($id) to soft delete section via repository, return JSON success response
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 9.8, 9.9, 9.10, 9.11, 9.12_
  
  - [x] 10.2 Add reorder method to Admin/HomeSectionController
    - Validate request: section_ids required array, each element integer and exists in home_sections
    - Delegate to HomeConfigurationService.reorderSections()
    - Return JSON success response
    - _Requirements: 10.1, 10.2, 10.3, 10.4_
  
  - [x] 10.3 Add toggleStatus method to Admin/HomeSectionController
    - Retrieve current section by ID
    - Toggle is_active status via HomeConfigurationService.toggleSectionStatus()
    - Return JSON response with new status
    - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [x] 11. Implement public API controller
  - [x] 11.1 Create HomeSectionResource
    - Transform data to include: uuid, type, title, display_order, configuration, rendered_data
    - Exclude internal fields: id, created_at, updated_at, deleted_at
    - _Requirements: 13.4, 13.5_
  
  - [x] 11.2 Create Api/HomeConfigurationController
    - Inject HomeConfigurationService
    - Implement index() to retrieve complete configuration via service
    - Cache response for 3600 seconds with tag 'home_sections'
    - Return JSON response using HomeSectionResource collection
    - Make endpoint publicly accessible (no authentication required)
    - _Requirements: 13.1, 13.2, 13.3, 13.6, 13.7_

- [x] 12. Register routes
  - [x] 12.1 Register admin routes
    - Add resource route for admin.home-sections with index, create, store, edit, update, destroy
    - Add POST route for admin.home-sections.reorder
    - Add POST route for admin.home-sections.toggle-status
    - Apply auth and admin middleware
  
  - [x] 12.2 Register API routes
    - Add GET route for api/home-configuration pointing to Api/HomeConfigurationController@index
    - Make route publicly accessible (no auth middleware)

- [x] 13. Implement service provider for dependency injection
  - [x] 13.1 Create HomePageServiceProvider
    - Bind HomeSectionRepositoryInterface to HomeSectionRepository
    - Register HomeConfigurationService as singleton
    - Register HomeSectionRendererService as singleton
    - Register all renderer implementations (HeroRenderer, FeaturedProductsRenderer, etc.)
    - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5_
  
  - [x] 13.2 Register HomePageServiceProvider in config/app.php
    - Add provider to providers array
    - _Requirements: 17.6_

- [ ] 14. Checkpoint - Ensure backend API works
  - Test admin CRUD operations create, read, update, delete sections
  - Test reorder endpoint updates display_order correctly
  - Test toggleStatus endpoint changes is_active field
  - Test public API endpoint returns cached configuration
  - Test cache invalidation when sections are modified
  - Ensure all tests pass, ask the user if questions arise

- [x] 15. Implement database seeder
  - [x] 15.1 Create HomeSectionSeeder
    - Create Hero section with type 'hero', display_order 1, is_active true, example configuration
    - Create Featured Products section with type 'featured_products', display_order 2, is_active true, example configuration
    - Create Featured Categories section with type 'featured_categories', display_order 3, is_active true, example configuration
    - Create Promo Banners section with type 'banners', display_order 4, is_active true, example configuration
    - Create Testimonials section with type 'testimonials', display_order 5, is_active true, example configuration
    - Create Custom HTML Block section with type 'html_block', display_order 6, is_active true, example configuration
    - Create sample HomeSectionItems for Featured Products and Featured Categories sections
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8_
  
  - [x] 15.2 Register seeder in DatabaseSeeder
    - Call HomeSectionSeeder in run() method

- [ ] 16. Implement activity logging
  - [ ] 16.1 Install spatie/laravel-activitylog package
    - Add package via composer
    - Publish migrations and run them
    - _Requirements: 19.6_
  
  - [ ] 16.2 Add activity logging to HomeSection model
    - Use LogsActivity trait
    - Configure logged attributes and events
    - _Requirements: 19.1, 19.2, 19.3_
  
  - [ ] 16.3 Add activity logging to service methods
    - Log activity in toggleSectionStatus with description "Toggled status for home section: {title}"
    - Log activity in reorderSections with description "Reordered home sections"
    - Associate logs with authenticated admin user
    - _Requirements: 19.4, 19.5, 19.7_

- [x] 17. Implement admin frontend interface with Vue/Inertia
  - [x] 17.1 Create Admin/HomeSections/Index.vue component
    - Display table with columns: title, type, status (toggle switch), display_order, actions (edit, delete)
    - Implement drag and drop for reordering using a library (e.g., Sortable.js or Vue Draggable)
    - Send AJAX request to reorder endpoint when sections are dragged
    - Implement toggle switch that sends AJAX request to toggleStatus endpoint
    - Display loading spinners during AJAX operations
    - Display success/error toast notifications
    - Provide "Create New Section" button
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 15.10, 10.5, 10.6, 10.7, 11.5, 11.6, 11.7_
  
  - [x] 17.2 Create Admin/HomeSections/Create.vue component
    - Form with fields: type (select), title (text), is_active (checkbox), display_order (number), configuration (dynamic based on type)
    - Submit form to store endpoint
    - Display validation errors
    - Redirect to index on success
  
  - [x] 17.3 Create Admin/HomeSections/Edit.vue component
    - Form with fields: type (select), title (text), is_active (checkbox), display_order (number), configuration (dynamic based on type)
    - Pre-populate form with existing section data
    - Submit form to update endpoint
    - Display validation errors
    - Redirect to index on success

- [x] 18. Implement public frontend home page with Vue/Inertia
  - [x] 18.1 Create Home.vue component
    - Fetch configuration from API endpoint on component mount
    - Render sections in order specified by display_order
    - Use dynamic component rendering based on section type
    - _Requirements: 16.1, 16.2, 16.3, 16.4_
  
  - [x] 18.2 Create HeroSection.vue component
    - Display title, subtitle, background image/video, CTA buttons
    - Apply overlay opacity from configuration
    - _Requirements: 16.5_
  
  - [x] 18.3 Create FeaturedProductsSection.vue component
    - Display product cards with images, titles, prices, ratings
    - Support grid layout with configurable columns
    - Link to product detail pages
    - _Requirements: 16.6_
  
  - [x] 18.4 Create FeaturedCategoriesSection.vue component
    - Display category cards with images, titles, product counts
    - Support grid layout with configurable columns
    - Link to category pages
    - _Requirements: 16.7_
  
  - [x] 18.5 Create BannersSection.vue component
    - Display promotional banners in slider or grid layout
    - Support autoplay with configurable speed
    - _Requirements: 16.8_
  
  - [x] 18.6 Create TestimonialsSection.vue component
    - Display customer testimonials with names, ratings, avatars
    - Support carousel layout
    - _Requirements: 16.9_
  
  - [x] 18.7 Create HtmlBlockSection.vue component
    - Render custom HTML content safely using v-html
    - Apply custom CSS classes from configuration
    - _Requirements: 16.10_

- [ ] 19. Implement error handling
  - [ ] 19.1 Add error handling to API controller
    - Wrap service calls in try-catch
    - Return JSON error responses with appropriate status codes
    - Log errors for debugging
    - _Requirements: 18.4_
  
  - [ ] 19.2 Add error handling to admin controller
    - Handle ModelNotFoundException and return 404 responses
    - Handle validation errors and return 422 responses
    - Display user-friendly error messages
    - _Requirements: 18.2, 18.3_
  
  - [ ] 19.3 Add error handling to repository
    - Handle cache failures gracefully by logging and continuing without cache
    - _Requirements: 18.5_
  
  - [ ] 19.4 Add error handling to renderers
    - Handle null polymorphic relationships by excluding items
    - _Requirements: 18.6_

- [ ] 20. Final checkpoint and integration testing
  - Test complete admin workflow: create, edit, reorder, toggle, delete sections
  - Test public API returns correct cached data
  - Test cache invalidation triggers on all model events
  - Test frontend renders all section types correctly
  - Test drag and drop reordering updates display order
  - Test toggle switches update section status
  - Verify activity logs are created for all operations
  - Verify performance with database indexes and caching
  - Ensure all tests pass, ask the user if questions arise

## Notes

- This implementation follows a bottom-up approach: database → models → repositories → services → controllers → frontend
- Each phase builds on the previous one, ensuring incremental progress
- Checkpoints are included to validate functionality before moving to the next phase
- All tasks reference specific requirements for traceability
- The Strategy Pattern for renderers allows easy addition of new section types in the future
- Caching with tag-based invalidation ensures optimal performance while maintaining data freshness
- Activity logging provides audit trail for administrative actions
