<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CategoryServiceInterface::class);
    }

    public function test_can_create_category(): void
    {
        $data = [
            'name' => 'Test Category',
            'description' => 'Test description',
            'is_active' => true,
        ];

        $category = $this->service->createCategory($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertTrue($category->is_active);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Original Name']);

        $result = $this->service->updateCategory($category->id, [
            'name' => 'Updated Name',
        ]);

        $this->assertTrue($result);
        $this->assertEquals('Updated Name', $category->fresh()->name);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->deleteCategory($category->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_can_toggle_category_status(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->service->toggleStatus($category->id);

        $this->assertFalse($category->fresh()->is_active);

        $this->service->toggleStatus($category->id);

        $this->assertTrue($category->fresh()->is_active);
    }

    public function test_can_get_active_categories(): void
    {
        Category::factory()->count(3)->active()->create();
        Category::factory()->count(2)->inactive()->create();

        $activeCategories = $this->service->getActiveCategories();

        $this->assertCount(3, $activeCategories);
    }

    public function test_can_search_categories(): void
    {
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Clothing']);
        Category::factory()->create(['name' => 'Books']);

        $results = $this->service->getPaginatedCategories(15, 'Electr');

        $this->assertCount(1, $results);
        $this->assertEquals('Electronics', $results->first()->name);
    }
}
