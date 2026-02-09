<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products?per_page=15');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('name', $data[0]);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $product->id, 'name' => $product->name]);
    }

    public function test_can_create_product_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del producto',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJson(['name' => 'Nuevo Producto', 'price' => '99.99']);

        $this->assertDatabaseHas('products', ['name' => 'Nuevo Producto']);
    }

    public function test_cannot_create_product_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Nuevo Producto',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_product_when_authenticated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/products/{$product->id}", [
            'name' => 'Producto Actualizado',
        ]);

        $response->assertStatus(200)
            ->assertJson(['name' => 'Producto Actualizado']);

        $this->assertDatabaseHas('products', ['name' => 'Producto Actualizado']);
    }

    public function test_can_delete_product_when_authenticated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
