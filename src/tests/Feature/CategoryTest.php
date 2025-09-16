<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;
use App\Models\User;


class CategoryTest extends TestCase
{
    use RefreshDatabase;

      protected function setUp(): void
    {
        parent::setUp();

        // Tạo và đăng nhập user mặc định cho toàn bộ test
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_list_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]);
    }

    public function test_can_create_category()
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test Description',
            'status' => true
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonFragment($categoryData);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_category_creation_requires_name()
    {
        $response = $this->postJson('/api/categories', [
            'slug' => 'test-slug'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_category_creation_requires_slug()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('slug');
    }

    public function test_category_slug_must_be_unique()
    {
        Category::factory()->create(['slug' => 'existing-slug']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Test Category',
            'slug' => 'existing-slug'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('slug');
    }

    public function test_can_show_single_category()
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create();
        $category->products()->attach($products);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
                'description',
                'status',
                'products' => [
                    '*' => ['id', 'name', 'description', 'price', 'stock']
                ]
            ]);

        $this->assertCount(3, $response->json('products'));
    }

    public function test_can_update_category()
    {
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'Updated Description',
            'status' => false
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);

        $this->assertDatabaseHas('categories', array_merge(
            ['id' => $category->id],
            $updateData
        ));
    }

    public function test_can_update_category_with_same_slug()
    {
        $category = Category::factory()->create(['slug' => 'original-slug']);

        $updateData = [
            'name' => 'Updated Category',
            'slug' => 'original-slug' // Same slug should be allowed
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200);
    }

    public function test_cannot_update_category_with_existing_slug()
    {
        $category1 = Category::factory()->create(['slug' => 'slug-1']);
        $category2 = Category::factory()->create(['slug' => 'slug-2']);

        $response = $this->putJson("/api/categories/{$category2->id}", [
            'name' => 'Updated Category',
            'slug' => 'slug-1' // Trying to use existing slug
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('slug');
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_not_found()
    {
        $response = $this->getJson('/api/categories/999');
        $response->assertStatus(404);
    }

    public function test_pagination_works()
    {
        Category::factory()->count(25)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data') // Default pagination is 10
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

    public function test_can_create_category_without_description()
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'status' => true
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_can_create_category_without_status()
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category'
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201);
    }
}
