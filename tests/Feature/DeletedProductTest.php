<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletedProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'price' => 10000,
            'wholesale_price' => 8000,
            'unit' => 'pcs'
        ]);

        $this->customer1 = User::factory()->create([
            'name' => 'Customer 1',
            'email' => 'customer1@test.com',
            'role' => 'customer'
        ]);

        $this->customer2 = User::factory()->create([
            'name' => 'Customer 2',
            'email' => 'customer2@test.com',
            'role' => 'customer'
        ]);
    }

    /** @test */
    public function it_removes_product_from_single_cart_when_deleted()
    {
        // Customer adds product to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        // Verify cart has the product
        $this->assertEquals(1, Cart::where('user_id', $this->customer1->id)->count());

        // Admin deletes the product
        $this->product->delete();

        // Verify cart is now empty
        $this->assertEquals(0, Cart::where('user_id', $this->customer1->id)->count());
    }

    /** @test */
    public function it_removes_product_from_multiple_carts_when_deleted()
    {
        // Multiple customers add the same product to their carts
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        Cart::create([
            'user_id' => $this->customer2->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price_snapshot' => 10000
        ]);

        // Verify both carts have the product
        $this->assertEquals(1, Cart::where('user_id', $this->customer1->id)->count());
        $this->assertEquals(1, Cart::where('user_id', $this->customer2->id)->count());
        $this->assertEquals(2, Cart::where('product_id', $this->product->id)->count());

        // Admin deletes the product
        $this->product->delete();

        // Verify product is removed from ALL carts
        $this->assertEquals(0, Cart::where('user_id', $this->customer1->id)->count());
        $this->assertEquals(0, Cart::where('user_id', $this->customer2->id)->count());
        $this->assertEquals(0, Cart::where('product_id', $this->product->id)->count());
    }

    /** @test */
    public function it_keeps_other_products_in_cart_when_one_is_deleted()
    {
        // Create another product
        $product2 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product 2',
            'slug' => 'test-product-2',
            'description' => 'Test Description 2',
            'price' => 15000,
            'wholesale_price' => 12000,
            'unit' => 'pcs'
        ]);

        // Customer adds both products to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price_snapshot' => 15000
        ]);

        // Verify cart has 2 products
        $this->assertEquals(2, Cart::where('user_id', $this->customer1->id)->count());

        // Admin deletes first product
        $this->product->delete();

        // Verify only first product is removed, second product remains
        $this->assertEquals(1, Cart::where('user_id', $this->customer1->id)->count());
        $this->assertEquals(0, Cart::where('product_id', $this->product->id)->count());
        $this->assertEquals(1, Cart::where('product_id', $product2->id)->count());
    }

    /** @test */
    public function cart_page_works_after_product_deletion()
    {
        // Customer adds product to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        // Admin deletes the product
        $this->product->delete();

        // Customer visits cart page - should not error
        $response = $this->actingAs($this->customer1)
            ->get('/keranjang');

        $response->assertStatus(200);
        // Cart should be empty or show appropriate message
    }

    /** @test */
    public function checkout_fails_gracefully_after_product_deletion()
    {
        // Customer adds product to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        // Admin deletes the product (which should remove it from cart)
        $this->product->delete();

        // Customer tries to checkout - should fail gracefully
        $response = $this->actingAs($this->customer1)
            ->get('/checkout');

        // Should redirect to cart with error message
        $response->assertRedirect('/keranjang');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function deleting_event_is_triggered()
    {
        // Add product to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        $cartCountBefore = Cart::count();
        $this->assertEquals(1, $cartCountBefore);

        // Delete product (should trigger deleting event)
        $this->product->delete();

        $cartCountAfter = Cart::count();
        $this->assertEquals(0, $cartCountAfter);
    }
}
