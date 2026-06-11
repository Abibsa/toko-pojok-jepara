<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function notifyLowStock(Product $product)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            // Create database notification
            $admin->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\LowStockNotification',
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->stock->quantity,
                    'message' => "Stok produk {$product->name} tinggal {$product->stock->quantity} unit",
                    'action_url' => route('admin.stok.index', ['search' => $product->name], false)
                ],
                'read_at' => null
            ]);
        }
    }

    public function notifyNewOrder(\App\Models\Order $order)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $admin->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\NewOrderNotification',
                'data' => [
                    'type' => 'success',
                    'title' => 'Pesanan Baru Masuk!',
                    'order_code' => $order->order_code,
                    'message' => "Pesanan baru {$order->order_code} senilai Rp " . number_format($order->total_amount, 0, ',', '.') . " telah diterima.",
                    'action_url' => route('admin.pesanan.show', $order->id, false)
                ],
                'read_at' => null
            ]);
        }
    }

    public function notifyOrderCancelled(\App\Models\Order $order)
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $admin->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\OrderCancelledNotification',
                'data' => [
                    'type' => 'error',
                    'title' => 'Pesanan Dibatalkan',
                    'order_code' => $order->order_code,
                    'message' => "Pesanan {$order->order_code} telah dibatalkan oleh pelanggan.",
                    'action_url' => route('admin.pesanan.show', $order->id, false)
                ],
                'read_at' => null
            ]);
        }
    }

    public function notifyStockSubscribers(Product $product)
    {
        $subscribers = StockAlert::where('product_id', $product->id)
            ->where('is_notified', false)
            ->with('user')
            ->get();

        foreach ($subscribers as $alert) {
            // Create notification for customer
            $alert->user->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\StockAvailableNotification',
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'message' => "Produk {$product->name} sudah tersedia kembali!"
                ],
                'read_at' => null
            ]);

            // Mark as notified
            $alert->update(['is_notified' => true]);
        }
    }

    public function subscribeToStockAlert(Product $product, User $user)
    {
        $alert = StockAlert::firstOrCreate([
            'product_id' => $product->id,
            'user_id' => $user->id
        ], [
            'is_notified' => false
        ]);

        // Always notify admin when customer clicks "Beritahu"
        $this->notifyAdminOfStockRequest($product, $user);

        return $alert;
    }

    public function notifyAdminOfStockRequest(Product $product, User $user)
    {
        $admins = User::where('role', 'admin')->get();
        
        // Count how many people are waiting for this product
        $waitingCount = StockAlert::where('product_id', $product->id)
            ->where('is_notified', false)
            ->count();
        
        foreach ($admins as $admin) {
            $admin->notifications()->create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\StockRequestNotification',
                'data' => [
                    'type' => 'warning',
                    'title' => 'Permintaan Restock Barang!',
                    'product_id' => $product->id,
                    'message' => "Pelanggan {$user->name} menekan tombol 'Beritahu Saya' untuk produk {$product->name}. Saat ini ada {$waitingCount} orang yang menunggu barang ini.",
                    'action_url' => route('admin.stok.index', ['search' => $product->name], false)
                ],
                'read_at' => null
            ]);
        }
    }

    public function unsubscribeFromStockAlert(Product $product, User $user)
    {
        return StockAlert::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markNotificationAsRead($notificationId, User $user)
    {
        return $user->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    public function deleteNotification($notificationId, User $user)
    {
        return $user->notifications()
            ->where('id', $notificationId)
            ->delete();
    }

    public function markAllNotificationsAsRead(User $user)
    {
        return $user->unreadNotifications()
            ->update(['read_at' => now()]);
    }

    public function getNotificationCount(User $user)
    {
        return $user->unreadNotifications()->count();
    }
}