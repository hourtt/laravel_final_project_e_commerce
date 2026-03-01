<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_to_cart_if_stock_allows()
    {
        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'Phones']);
        $product = \App\Models\Product::create([
            'name' => 'iphone 14',
            'description' => 'Test phone',
            'price' => 1000,
            'stock' => 5,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user)->postJson('/cart/add', [
            'product_id' => $product->id
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $cart = session()->get('cart');
        $this->assertEquals(1, $cart[$product->id]['quantity']);
    }

    public function test_cannot_add_to_cart_if_stock_exceeded()
    {
        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'Phones']);
        $product = \App\Models\Product::create([
            'name' => 'iphone 14',
            'description' => 'Test phone',
            'price' => 1000,
            'stock' => 1,
            'category_id' => $category->id
        ]);

        // Add 1 (success)
        $this->actingAs($user)->postJson('/cart/add', ['product_id' => $product->id]);

        // Add 2 (fail)
        $response = $this->actingAs($user)->postJson('/cart/add', ['product_id' => $product->id]);
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }

    public function test_cart_update_limits_stock()
    {
        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'Phones']);
        $product = \App\Models\Product::create([
            'name' => 'iphone 14',
            'description' => 'Test phone',
            'price' => 1000,
            'stock' => 5,
            'category_id' => $category->id
        ]);

        $this->actingAs($user)->withSession(['cart' => [
            $product->id => ['quantity' => 1]
        ]])->postJson('/cart/update', [
            'product_id' => $product->id,
            'quantity' => 10 // Above stock
        ])->assertStatus(400)->assertJson(['success' => false]);
    }
}
