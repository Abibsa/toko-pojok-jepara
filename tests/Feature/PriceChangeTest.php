<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceChangeTest extends TestCase
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

        // Add stock for the product
        Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 100
        ]);

        $this->customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'role' => 'customer'
        ]);
    }

    /** @test */
    public function it_saves_price_snapshot_when_adding_to_cart()
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/keranjang/tambah', [
                'product_id' => $this->product->id,
                'quantity' => 1
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $cart = Cart::where('user_id', $this->customer->id)
            ->where('product_id', $this->product->id)
            ->first();

        $this->assertNotNull($cart);
        $this->assertEquals(10000, $cart->price_snapshot);
    }

    /** @test */
    public function it_detects_price_increase()
    {
        // Add product to cart with original price
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_snapshot' => 10000
        ]);

        // Admin changes price (increase)
        $this->product->update(['price' => 12000]);
        $this->product->refresh();

        // Check if price change is detected
        $cart->refresh();
        $this->assertTrue($cart->hasPriceChanged());

        $priceChange = $cart->price_change;
        $this->assertEquals(10000, $priceChange['old_price']);
        $this->assertEquals(12000, $priceChange['new_price']);
        $this->assertEquals(2000, $priceChange['difference']);
        $this->assertEquals('increase', $priceChange['type']);
        $this->assertEquals(20.0, $priceChange['percentage']);
    }

    /** @test */
    public function it_detects_price_decrease()
    {
        // Add product to cart with original price
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_snapshot' => 10000
        ]);

        // Admin changes price (decrease)
        $this->product->update(['price' => 8000]);
        $this->product->refresh();

        // Check if price change is detected
        $cart->refresh();
        $this->assertTrue($cart->hasPriceChanged());

        $priceChange = $cart->price_change;
        $this->assertEquals(10000, $priceChange['old_price']);
        $this->assertEquals(8000, $priceChange['new_price']);
        $this->assertEquals(-2000, $priceChange['difference']);
        $this->assertEquals('decrease', $priceChange['type']);
        $this->assertEquals(-20.0, $priceChange['percentage']);
    }

    /** @test */
    public function it_returns_false_when_price_unchanged()
    {
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_snapshot' => 10000
        ]);

        // Price remains the same
        $this->assertFalse($cart->hasPriceChanged());
        $this->assertNull($cart->price_change);
    }

    /** @test */
    public function it_handles_wholesale_price_changes()
    {
        // Add 5 items (wholesale price applies)
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'price_snapshot' => 8000 // Original wholesale price
        ]);

        // Admin changes wholesale price
        $this->product->update(['wholesale_price' => 9000]);
        $this->product->refresh();

        $cart->refresh();
        $this->assertTrue($cart->hasPriceChanged());

        $priceChange = $cart->price_change;
        $this->assertEquals(8000, $priceChange['old_price']);
        $this->assertEquals(9000, $priceChange['new_price']);
        $this->assertEquals('increase', $priceChange['type']);
    }

    /** @test */
    public function cart_view_displays_price_change_warning()
    {
        // Add product to cart
        Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_snapshot' => 10000
        ]);

        // Change price
        $this->product->update(['price' => 12000]);

        // Visit cart page
        $response = $this->actingAs($this->customer)
            ->get('/keranjang');

        $response->assertStatus(200);
        $response->assertSee('Harga telah berubah');
        $response->assertSee('10.000'); // Old price
        $response->assertSee('12.000'); // New price
    }
}
