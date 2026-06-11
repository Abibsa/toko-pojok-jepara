<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create(['role' => 'customer']);
        $category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
        ]);
        
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Test',
            'slug' => 'produk-test',
            'price' => 10000,
            'wholesale_price' => 9000,
        ]);
        
        Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 50,
        ]);
    }

    public function test_customer_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->customer)->postJson(route('keranjang.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_add_more_than_available_stock()
    {
        $response = $this->actingAs($this->customer)->postJson(route('keranjang.add'), [
            'product_id' => $this->product->id,
            'quantity' => 100, // Stock is only 50
        ]);

        $response->assertStatus(400); // Because it exceeds stock
        $this->assertDatabaseMissing('carts', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_customer_can_update_cart_quantity()
    {
        Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->patchJson(route('keranjang.update'), [
            'cart_id' => Cart::first()->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }

    public function test_customer_can_remove_item_from_cart()
    {
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->deleteJson(route('keranjang.remove'), [
            'cart_id' => $cart->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('carts', [
            'id' => $cart->id,
        ]);
    }

    public function test_customer_can_clear_cart()
    {
        Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->deleteJson(route('keranjang.clear'));

        $response->assertStatus(200);
        $this->assertDatabaseCount('carts', 0);
    }
}
