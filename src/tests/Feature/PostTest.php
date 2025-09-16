<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Tests\TestCase;
use App\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo và đăng nhập user mặc định cho toàn bộ test
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }


    public function test_can_list_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'author',
                        'published_at',
                        'created_at',
                        'updated_at',
                        'images'
                    ]
                ],
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]);
    }

    public function test_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'author' => 'Test Author',
            'published_at' => '2024-01-01'
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'author' => $postData['author']
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'author' => $postData['author']
        ]);
    }

    public function test_can_create_post_with_images()
    {
        Storage::fake('public');

        $image1 = UploadedFile::fake()->create('post1.jpg', 100);
        $image2 = UploadedFile::fake()->create('post2.jpg', 200);

        $postData = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'author' => 'Test Author',
            'images' => [$image1, $image2]
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201);

        $post = Post::latest()->first();
        $this->assertCount(2, $post->images);

        Storage::disk('public')->assertExists('uploads/' . $image1->hashName());
        Storage::disk('public')->assertExists('uploads/' . $image2->hashName());
    }

    public function test_post_creation_requires_title()
    {
        $response = $this->postJson('/api/posts', [
            'content' => 'Test content',
            'author' => 'Test Author'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_post_creation_requires_content()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Title',
            'author' => 'Test Author'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('content');
    }

    public function test_post_creation_requires_author()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Title',
            'content' => 'Test content'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('author');
    }

    public function test_can_create_post_without_published_date()
    {
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'author' => 'Test Author'
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'published_at' => null
        ]);
    }

    public function test_can_show_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => $post->author
            ])
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'author',
                'published_at',
                'images'
            ]);
    }

    public function test_can_update_post()
    {
        $post = Post::factory()->create();

        $updateData = [
            'title' => 'Updated Post Title',
            'content' => 'Updated content',
            'author' => 'Updated Author',
            'published_at' => '2024-02-01'
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => $updateData['title'],
                'content' => $updateData['content'],
                'author' => $updateData['author']
            ]);

        $this->assertDatabaseHas('posts', array_merge(
            ['id' => $post->id],
            $updateData
        ));
    }

    public function test_can_update_post_with_images()
    {
        Storage::fake('public');

        $post = Post::factory()->create();
        $image = UploadedFile::fake()->create('updated.jpg',100);

        $updateData = [
            'title' => 'Updated Post',
            'content' => 'Updated content',
            'author' => 'Updated Author',
            'images' => [$image]
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200);

        $post->refresh();
        $this->assertCount(1, $post->images);
        Storage::disk('public')->assertExists('uploads/' . $image->hashName());
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_can_delete_post_with_images()
    {
        Storage::fake('public');

        $post = Post::factory()->create();
        $post->images()->create(['path' => 'uploads/test-image.jpg']);

        // Create fake file
        Storage::disk('public')->put('uploads/test-image.jpg', 'fake-content');

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing('uploads/test-image.jpg');
    }

    public function test_post_not_found()
    {
        $response = $this->getJson('/api/posts/999');
        $response->assertStatus(404);
    }

    public function test_pagination_works()
    {
        Post::factory()->count(25)->create();

        $response = $this->getJson('/api/posts');

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

    public function test_invalid_image_upload()
    {
        Storage::fake('public');

        $nonImageFile = UploadedFile::fake()->create('document.pdf', 100);

        $postData = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'author' => 'Test Author',
            'images' => [$nonImageFile]
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('images.0');
    }
}
