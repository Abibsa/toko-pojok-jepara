<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\StockAlert;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockNotificationTest extends TestCase
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

        Stock::create([
            'product_id' => $this->product->id,
            'quantity' => 0 // Out of stock
        ]);

        $this->customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'role' => 'customer'
        ]);

        $this->stockService = app(StockService::class);
    }

    /** @test */
    public function customer_can_subscribe_to_stock_alert()
    {
        // Customer subscribes to stock alert
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();

        $this->assertNotNull($alert);
        $this->assertFalse($alert->is_notified);
    }

    /** @test */
    public function notification_is_sent_when_stock_is_restocked()
    {
        // Customer subscribes to stock alert
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        // Admin restocks product (from 0 to 10)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock', $admin->id);

        // Check if notification was created
        $notification = $this->customer->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('App\Notifications\StockAvailableNotification', $notification->type);
        $this->assertEquals($this->product->id, $notification->data['product_id']);

        // Check if alert is marked as notified
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();
        $this->assertTrue($alert->is_notified);
    }

    /** @test */
    public function notification_is_not_sent_when_stock_remains_above_zero()
    {
        // Set initial stock to 5
        Stock::where('product_id', $this->product->id)->update(['quantity' => 5]);

        // Customer subscribes
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        // Admin adds more stock (from 5 to 15) - should NOT trigger notification
        $admin = User::factory()->create(['role' => 'admin']);
        $this->stockService->updateStock($this->product, 10, 'in', 'Additional stock', $admin->id);

        // No notification should be created
        $notificationCount = $this->customer->notifications()->count();
        $this->assertEquals(0, $notificationCount);
    }

    /** @test */
    public function notification_is_sent_only_once()
    {
        // Customer subscribes
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        // First restock (0 to 10)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->stockService->updateStock($this->product, 10, 'in', 'First restock', $admin->id);

        // Check notification was sent
        $this->assertEquals(1, $this->customer->notifications()->count());

        // Alert should be marked as notified
        $alert = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();
        $this->assertTrue($alert->is_notified);

        // Second restock (10 to 20) - should NOT send another notification
        $this->stockService->updateStock($this->product, 10, 'in', 'Second restock', $admin->id);

        // Still only 1 notification
        $this->assertEquals(1, $this->customer->notifications()->count());
    }

    /** @test */
    public function multiple_customers_receive_notifications()
    {
        $customer2 = User::factory()->create([
            'name' => 'Customer 2',
            'email' => 'customer2@test.com',
            'role' => 'customer'
        ]);

        // Both customers subscribe
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $customer2->id,
            'is_notified' => false
        ]);

        // Admin restocks
        $admin = User::factory()->create(['role' => 'admin']);
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock', $admin->id);

        // Both customers should receive notifications
        $this->assertEquals(1, $this->customer->notifications()->count());
        $this->assertEquals(1, $customer2->notifications()->count());

        // Both alerts should be marked as notified
        $alert1 = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();
        $alert2 = StockAlert::where('product_id', $this->product->id)
            ->where('user_id', $customer2->id)
            ->first();

        $this->assertTrue($alert1->is_notified);
        $this->assertTrue($alert2->is_notified);
    }

    /** @test */
    public function notification_contains_correct_product_information()
    {
        // Customer subscribes
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        // Admin restocks
        $admin = User::factory()->create(['role' => 'admin']);
        $this->stockService->updateStock($this->product, 10, 'in', 'Restock', $admin->id);

        // Check notification data
        $notification = $this->customer->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals($this->product->id, $notification->data['product_id']);
        $this->assertEquals($this->product->name, $notification->data['product_name']);
        $this->assertStringContainsString('tersedia kembali', $notification->data['message']);
    }

    /** @test */
    public function stock_service_triggers_notification_correctly()
    {
        // Customer subscribes
        StockAlert::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'is_notified' => false
        ]);

        // Verify initial stock is 0
        $stock = Stock::where('product_id', $this->product->id)->first();
        $this->assertEquals(0, $stock->quantity);

        // Restock using StockService
        $admin = User::factory()->create(['role' => 'admin']);
        $result = $this->stockService->updateStock($this->product, 15, 'in', 'Restock test', $admin->id);
        $this->assertTrue($result);

        // Verify stock was updated
        $stock->refresh();
        $this->assertEquals(15, $stock->quantity);

        // Verify notification was sent
        $this->assertEquals(1, $this->customer->notifications()->count());
    }
}
