<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    
     public function test_can_list_products()
     {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/products');
        $response->assertStatus(200)
        ->assertJsonCount(3);
     }
}
