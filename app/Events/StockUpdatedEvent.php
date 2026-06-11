<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productId;
    public $quantity;
    public $statusColor;
    public $statusText;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product)
    {
        $this->productId = $product->id;
        $this->quantity = $product->stock ? $product->stock->quantity : 0;
        
        $statusInfo = $product->stock_status;
        $this->statusColor = $statusInfo['color'];
        $this->statusText = $statusInfo['status'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('stock.' . $this->productId),
        ];
    }
    
    public function broadcastAs()
    {
        return 'stock.updated';
    }
}
