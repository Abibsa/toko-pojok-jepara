<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically complete shipped orders that have passed the 24 hours threshold';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for shipped orders to auto-complete...');

        // Find orders that are shipped, and shipped_at is older than 24 hours
        // and only for delivery (pickup_method == 'delivery' or null)
        $orders = Order::where('status', 'shipped')
            ->where(function($query) {
                $query->where('pickup_method', 'delivery')
                      ->orWhereNull('pickup_method');
            })
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', Carbon::now()->subHours(24))
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders to auto-complete.');
            return;
        }

        $count = 0;
        foreach ($orders as $order) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'note' => ($order->note ? $order->note . ' | ' : '') . 'Selesai otomatis oleh sistem (melewati 24 jam setelah dikirim) pada ' . now()->format('d/m/Y H:i')
            ]);
            $count++;
            $this->line("Order {$order->order_code} has been auto-completed.");
        }

        $this->info("Successfully auto-completed {$count} order(s).");
    }
}
