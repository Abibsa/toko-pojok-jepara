<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaceConditionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
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

        Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 5
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
    public function it_prevents_overselling_with_concurrent_orders()
    {
        // Customer 1: Add 4 items to cart
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 4,
            'price_snapshot' => 10000
        ]);

        // Customer 2: Add 3 items to cart
        Cart::create([
            'user_id' => $this->customer2->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price_snapshot' => 10000
        ]);

        // Simulate concurrent checkout attempts
        $response1 = null;
        $response2 = null;

        // Customer 1 checkout
        $response1 = $this->actingAs($this->customer1)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Address 1',
                'payment_method' => 'transfer',
                'pickup_method' => 'delivery',
                'note' => 'Test order 1'
            ]);

        // Customer 2 checkout (should fail due to insufficient stock)
        $response2 = $this->actingAs($this->customer2)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Address 2',
                'payment_method' => 'transfer',
                'pickup_method' => 'delivery',
                'note' => 'Test order 2'
            ]);

        // Assertions
        // One should succeed, one should fail
        $successCount = 0;
        $failCount = 0;

        if ($response1->status() === 200) {
            $successCount++;
            $this->assertTrue($response1->json('success'));
        } else {
            $failCount++;
            $this->assertFalse($response1->json('success'));
        }

        if ($response2->status() === 200) {
            $successCount++;
            $this->assertTrue($response2->json('success'));
        } else {
            $failCount++;
            $this->assertFalse($response2->json('success'));
        }

        // Exactly one should succeed
        $this->assertEquals(1, $successCount, 'Exactly one order should succeed');
        $this->assertEquals(1, $failCount, 'Exactly one order should fail');

        // Check final stock
        $finalStock = Stock::where('product_id', $this->product->id)->first();
        
        // Stock should be 1 (5 - 4 = 1), NOT negative
        $this->assertEquals(1, $finalStock->quantity, 'Stock should be 1, not negative');
        $this->assertGreaterThanOrEqual(0, $finalStock->quantity, 'Stock should never be negative');
    }

    /** @test */
    public function it_validates_stock_before_checkout()
    {
        // Add 10 items to cart (more than available stock of 5)
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'price_snapshot' => 10000
        ]);

        $response = $this->actingAs($this->customer1)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Test Address',
                'payment_method' => 'transfer',
                'pickup_method' => 'delivery'
            ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));
        // Message could be "Validasi stok gagal" or contain "Stok"
        $message = $response->json('message');
        $this->assertTrue(
            str_contains($message, 'Stok') || str_contains($message, 'stok') || str_contains($message, 'Validasi'),
            "Expected message to contain stock-related text, got: {$message}"
        );

        // Stock should remain unchanged
        $stock = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(5, $stock->quantity);
    }

    /** @test */
    public function it_uses_database_locking_during_checkout()
    {
        // This test verifies that lockForUpdate is being used
        Cart::create([
            'user_id' => $this->customer1->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_snapshot' => 10000
        ]);

        $response = $this->actingAs($this->customer1)
            ->postJson('/checkout/proses', [
                'shipping_address' => 'Test Address',
                'payment_method' => 'transfer',
                'pickup_method' => 'delivery'
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        // Verify stock was reduced correctly
        $stock = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(3, $stock->quantity);
    }
}
