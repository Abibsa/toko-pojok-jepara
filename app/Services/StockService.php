<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockService
{
    public function updateStock(Product $product, int $quantity, string $type, string $note = null, $userId = null): bool
    {
        return DB::transaction(function () use ($product, $quantity, $type, $note, $userId) {
            $stock = Stock::where('product_id', $product->id)->lockForUpdate()->first();
            if (!$stock) {
                $stock = Stock::create([
                    'product_id' => $product->id,
                    'quantity' => 0
                ]);
            }

            $quantityBefore = $stock->quantity;
            $quantityChange = 0;

            switch ($type) {
                case 'in':
                    $quantityAfter = $quantityBefore + $quantity;
                    $quantityChange = $quantity;
                    break;
                case 'out':
                    $quantityAfter = max(0, $quantityBefore - $quantity);
                    $quantityChange = -$quantity;
                    break;
                case 'adjustment':
                    $quantityAfter = $quantity;
                    $quantityChange = $quantity - $quantityBefore;
                    break;
                default:
                    return false;
            }

            // Update stock
            $stock->update(['quantity' => $quantityAfter]);
            
            // Dispatch real-time event for websocket clients
            event(new \App\Events\StockUpdatedEvent($product));

            // Record history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'quantity_change' => $quantityChange,
                'note' => $note,
                'user_id' => $userId ?? Auth::id()
            ]);

            // Check for low stock notification
            if ($quantityAfter <= 10 && $quantityAfter > 0) {
                $this->sendLowStockNotification($product);
            }

            // Notify subscribers if stock is back
            if ($quantityBefore == 0 && $quantityAfter > 0) {
                app(NotificationService::class)->notifyStockSubscribers($product);
            }

            return true;
        });
    }

    public function validateCartStock(array $cartItems): array
    {
        $errors = [];
        $suggestions = [];

        foreach ($cartItems as $item) {
            $product = Product::with(['stock', 'category'])->find($item['product_id']);
            
            if (!$product) {
                $errors[] = "Produk tidak ditemukan";
                continue;
            }

            $availableStock = $product->stock ? $product->stock->quantity : 0;
            
            if ($availableStock < $item['quantity']) {
                if ($availableStock == 0) {
                    $errors[] = "Produk {$product->name} sudah habis";
                    
                    // Suggest alternatives from same category
                    $alternatives = Product::with('stock')
                        ->where('category_id', $product->category_id)
                        ->where('id', '!=', $product->id)
                        ->whereHas('stock', function($query) {
                            $query->where('quantity', '>', 0);
                        })
                        ->limit(3)
                        ->get();
                    
                    if ($alternatives->count() > 0) {
                        $suggestions[] = [
                            'product' => $product->name,
                            'alternatives' => $alternatives->pluck('name')->toArray()
                        ];
                    }
                } else {
                    $errors[] = "Produk {$product->name} hanya tersisa {$availableStock} unit, Anda memesan {$item['quantity']} unit";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'suggestions' => $suggestions
        ];
    }

    public function processOrderStock(array $orderItems): bool
    {
        return DB::transaction(function () use ($orderItems) {
            foreach ($orderItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan: {$item['product_id']}");
                }

                $success = $this->updateStock(
                    $product,
                    $item['quantity'],
                    'out',
                    "Penjualan - Order #{$item['order_code']}"
                );

                if (!$success) {
                    throw new \Exception("Gagal mengurangi stok produk: {$product->name}");
                }
            }

            return true;
        });
    }

    public function restoreOrderStock(array $orderItems, string $orderCode): bool
    {
        return DB::transaction(function () use ($orderItems, $orderCode) {
            foreach ($orderItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    continue;
                }

                $this->updateStock(
                    $product,
                    $item['quantity'],
                    'in',
                    "Pembatalan pesanan - Order #{$orderCode}"
                );
            }

            return true;
        });
    }

    public function getCriticalStockProducts()
    {
        return Product::with(['stock', 'category'])
            ->whereHas('stock', function($query) {
                $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
            })
            ->orderBy('name')
            ->get();
    }

    public function getOutOfStockProducts()
    {
        return Product::with(['stock', 'category'])
            ->whereHas('stock', function($query) {
                $query->where('quantity', '<=', 0);
            })
            ->orderBy('name')
            ->get();
    }

    public function getStockStatistics()
    {
        $totalProducts = Product::count();
        $criticalStock = Product::whereHas('stock', function($query) {
            $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
        })->count();
        
        $outOfStock = Product::whereHas('stock', function($query) {
            $query->where('quantity', '<=', 0);
        })->count();

        $availableStock = $totalProducts - $criticalStock - $outOfStock;

        return [
            'total_products' => $totalProducts,
            'available_stock' => $availableStock,
            'critical_stock' => $criticalStock,
            'out_of_stock' => $outOfStock
        ];
    }

    private function sendLowStockNotification(Product $product)
    {
        // Integrate with Laravel's notification system
        app(NotificationService::class)->notifyLowStock($product);
        \Log::info("Low stock alert sent for product: {$product->name} (Stock: {$product->stock->quantity})");
    }

    public function getRecentStockMovements(int $limit = 10)
    {
        return StockHistory::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}