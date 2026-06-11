<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Order $deliveryOrder;
    private Order $pickupOrder;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        
        $this->deliveryOrder = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'ORD-TEST-001',
            'status' => 'pending',
            'total_amount' => 50000,
            'payment_method' => 'transfer',
            'payment_status' => 'pending',
            'pickup_method' => 'delivery',
            'shipping_address' => 'Test Address',
        ]);

        $this->pickupOrder = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'ORD-TEST-002',
            'status' => 'menunggu_diambil',
            'total_amount' => 60000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'pickup_method' => 'pickup',
            'shipping_address' => 'Toko Pojok Jepara', // Add this to satisfy NOT NULL constraint
            'pickup_deadline' => now()->addDays(1),
        ]);
    }

    public function test_admin_can_update_order_status_to_shipped()
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.pesanan.update-status', $this->deliveryOrder->id), [
            'status' => 'shipped'
        ]);

        $response->assertRedirect();
        
        $this->deliveryOrder->refresh();
        $this->assertEquals('shipped', $this->deliveryOrder->status);
        $this->assertNotNull($this->deliveryOrder->shipped_at);
    }

    public function test_admin_can_mark_pickup_order_as_picked_up()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pesanan.picked-up', $this->pickupOrder->id));

        $response->assertRedirect();
        
        $this->pickupOrder->refresh();
        $this->assertEquals('completed', $this->pickupOrder->status);
        $this->assertEquals('paid', $this->pickupOrder->payment_status);
    }

    public function test_customer_can_mark_shipped_order_as_completed()
    {
        $this->deliveryOrder->update(['status' => 'shipped']);

        $response = $this->actingAs($this->customer)->patchJson(route('pesanan.complete', $this->deliveryOrder->order_code));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->deliveryOrder->refresh();
        $this->assertEquals('completed', $this->deliveryOrder->status);
        $this->assertEquals('paid', $this->deliveryOrder->payment_status);
    }

    public function test_customer_cannot_mark_pending_order_as_completed()
    {
        $response = $this->actingAs($this->customer)->patchJson(route('pesanan.complete', $this->deliveryOrder->order_code));

        $response->assertStatus(400); // Bad request because status is not shipped
        $response->assertJson(['success' => false]);

        $this->deliveryOrder->refresh();
        $this->assertEquals('pending', $this->deliveryOrder->status);
    }
}
