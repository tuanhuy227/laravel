<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;
use App\Models\User;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // public function test_can_list_products()
    // {
    //     Product::factory()->count(3)->create();

    //     $response = $this->getJson('/api/products');

    //     $response->assertStatus(200)
    //         ->assertJsonCount(3, 'data')
    //         ->assertJsonStructure([
    //             'data' => [
    //                 '*' => [
    //                     'id',
    //                     'name',
    //                     'description',
    //                     'price',
    //                     'stock',
    //                     'created_at',
    //                     'updated_at',
    //                     'images',
    //                     'categories'
    //                 ]
    //             ],
    //             'current_page',
    //             'last_page',
    //             'per_page',
    //             'total'
    //         ]);
    // }

      protected function setUp(): void
    {
        parent::setUp();

        // Tạo và đăng nhập user mặc định cho toàn bộ test
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }


    public function test_can_list_products() {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    public function test_can_create_product()
    {
        $categories = Category::factory()->count(2)->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'categories' => $categories->pluck('id')->toArray()
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
        ]);
    }

    // public function test_can_create_product_with_categories()
    // {
    //     $categories = Category::factory()->count(2)->create();

    //     $productData = [
    //         'name' => 'Test Product',
    //         'description' => 'Test Description',
    //         'price' => 99.99,
    //         'stock' => 10,
    //         'categories' => $categories->pluck('id')->toArray()
    //     ];

    //     $response = $this->postJson('/api/products', $productData);

    //     $response->assertStatus(201);

    //     $product = Product::latest()->first();
    //     $this->assertCount(2, $product->categories);
    // }

    public function test_can_create_product_with_images()
    {
        Storage::fake('public');

        $image1 = UploadedFile::fake()->create('test1.jpg', 100); // 100 KB
        $image2 = UploadedFile::fake()->create('test2.jpg', 200);

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10,
            'images' => [$image1, $image2]
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $product = Product::latest()->first();
        $this->assertCount(2, $product->images);

        Storage::disk('public')->assertExists('uploads/' . $image1->hashName());
        Storage::disk('public')->assertExists('uploads/' . $image2->hashName());
    }

    public function test_product_creation_validation()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422);
    }

    public function test_can_show_single_product()
    {
        $categories = Category::factory()->count(2)->create();
        $product = Product::factory()->create();
        $product->categories()->attach($categories);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'price',
                'stock',
                'categories' => [
                    '*' => ['id', 'name', 'slug']
                ],
                'images'
            ]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();
        $image1 = UploadedFile::fake()->create('test1.jpg', 100);
        $image2 = UploadedFile::fake()->create('test2.jpg', 200);
        $categories = Category::factory()->count(2)->create();

        $updateData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated Description',
            'price' => 199.99,
            'stock' => 20,
            'images' => [$image1, $image2],
            'categories' => $categories->pluck('id')->toArray()
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => 'Updated Product Name',
                'description' => 'Updated Description',
                'price' => 199.99,
                'stock' => 20,
        ]);

        $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'name' => 'Updated Product Name',
                'description' => 'Updated Description',
                'price' => 199.99,
                'stock' => 20,
        ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_can_delete_product_with_images()
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $product->images()->create(['path' => 'uploads/test-image.jpg']);

        // Create fake file
        Storage::disk('public')->put('uploads/test-image.jpg', 'fake-content');

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('uploads/test-image.jpg');
    }

    public function test_product_not_found()
    {
        $response = $this->getJson('/api/products/999');
        $response->assertStatus(404);
    }

    public function test_pagination_works()
    {
        Product::factory()->count(25)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }
}
