<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected ProductRepositoryInterface $repository;

    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ProductRepositoryInterface::class);
        $this->app->instance(ProductRepositoryInterface::class, $this->repository);
        $this->service = $this->app->make(ProductService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_get_all_returns_products_from_repository(): void
    {
        $products = Product::factory()->count(3)->make();

        $this->repository
            ->shouldReceive('all')
            ->once()
            ->with([])
            ->andReturn($products);

        $result = $this->service->getAll();

        $this->assertCount(3, $result);
    }

    public function test_find_returns_product_from_repository(): void
    {
        $product = Product::factory()->make(['id' => 1]);

        $this->repository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($product);

        $result = $this->service->find(1);

        $this->assertSame($product, $result);
    }

    public function test_create_delegates_to_repository(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => 29.99,
            'stock' => 10,
        ];
        $product = Product::factory()->make($data);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($product);

        $result = $this->service->create($data);

        $this->assertSame($product, $result);
    }

    public function test_update_delegates_to_repository(): void
    {
        $product = Product::factory()->make(['id' => 1]);
        $data = ['name' => 'Updated Name'];
        $updatedProduct = (clone $product)->fill($data);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with(Mockery::type(Product::class), $data)
            ->andReturn($updatedProduct);

        $result = $this->service->update($product, $data);

        $this->assertEquals('Updated Name', $result->name);
    }

    public function test_delete_delegates_to_repository(): void
    {
        $product = Product::factory()->make(['id' => 1]);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with(Mockery::type(Product::class))
            ->andReturn(true);

        $result = $this->service->delete($product);

        $this->assertTrue($result);
    }
}
