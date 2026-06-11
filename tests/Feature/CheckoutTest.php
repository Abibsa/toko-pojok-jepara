<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'phone' => '081234567890',
            'address' => 'Jl. Test Address 123'
        ]);
        
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
            'quantity' => 10,
        ]);

        Cart::create([
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_can_checkout_with_delivery()
    {
        $response = $this->actingAs($this->customer)->postJson(route('checkout.process'), [
            'payment_method' => 'transfer',
            'pickup_method' => 'delivery',
            'shipping_address' => 'Jl. Delivery 456',
            'note' => 'Tolong di test',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'pickup_method' => 'delivery',
            'shipping_address' => 'Jl. Delivery 456',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Stock should be reduced
        $this->assertEquals(8, $this->product->fresh()->stock->quantity);
        
        // Cart should be empty
        $this->assertDatabaseCount('carts', 0);
    }

    public function test_customer_can_checkout_with_pickup()
    {
        $response = $this->actingAs($this->customer)->postJson(route('checkout.process'), [
            'payment_method' => 'cod',
            'pickup_method' => 'pickup',
            'note' => 'BOPIS Test',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'status' => 'menunggu_diambil', // Since it's pickup, it might be pending or menunggu_diambil based on your CheckoutController
            'pickup_method' => 'pickup',
        ]);
    }

    public function test_checkout_fails_if_stock_insufficient()
    {
        // Add another 10 to cart, making total 12, but stock is only 10
        Cart::where('user_id', $this->customer->id)->update(['quantity' => 12]);

        $response = $this->actingAs($this->customer)->postJson(route('checkout.process'), [
            'payment_method' => 'transfer',
            'pickup_method' => 'delivery',
            'shipping_address' => 'Jl. Delivery 456',
        ]);

        $response->assertStatus(400); // Bad request because insufficient stock
        
        // Order shouldn't be created
        $this->assertDatabaseCount('orders', 0);
        
        // Stock shouldn't be reduced
        $this->assertEquals(10, $this->product->fresh()->stock->quantity);
    }
}
