<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\StockService;

class CheckReservationExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reserve:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and auto-cancel expired BOPIS reservations';

    /**
     * Execute the console command.
     */
    public function handle(StockService $stockService)
    {
        $this->info('Checking expired BOPIS reservations...');

        $expiredOrders = Order::where('status', 'menunggu_diambil')
            ->whereNotNull('pickup_deadline')
            ->where('pickup_deadline', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            $this->info("Canceling order: {$order->order_code}");
            
            // Format order items for stock restoration
            $orderItemsData = $order->orderItems->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ];
            })->toArray();

            // Restore stock
            $success = $stockService->restoreOrderStock($orderItemsData, 'Batal Pesanan', 'Otomatis: melewati batas waktu pengambilan 24 jam');

            if ($success) {
                // Update order status
                $order->update([
                    'status' => 'cancelled',
                    'note' => ($order->note ? $order->note . ' | ' : '') . 'Dibatalkan otomatis: melewati batas waktu pengambilan 24 jam'
                ]);
                
                $count++;
            } else {
                $this->error("Failed to restore stock for order: {$order->order_code}");
            }
        }

        $this->info("Successfully canceled {$count} expired reservations.");
    }
}
