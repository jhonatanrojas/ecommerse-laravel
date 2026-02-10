<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_categories_index(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
    }

    public function test_can_view_create_category_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
    }

    public function test_can_store_category(): void
    {
        $data = [
            'name' => 'New Category',
            'description' => 'Category description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.categories.store'), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    public function test_cannot_store_category_without_nombre(): void
    {
        $data = [
            'description' => 'Category description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.categories.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_view_edit_category_form(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
        $response->assertViewHas('category', $category);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.categories.update', $category->id), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_can_toggle_category_status(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.categories.toggle-status', $category->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_can_search_categories(): void
    {
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Clothing']);

        $response = $this->actingAs($this->user)
            ->get(route('admin.categories.index', ['search' => 'Electr']));

        $response->assertStatus(200);
        $response->assertSee('Electronics');
        $response->assertDontSee('Clothing');
    }

    public function test_guest_cannot_access_categories(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }
}
