<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $cartItems = Cart::with(['product.stock', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang Anda kosong');
        }

        // Validate stock before showing checkout
        $cartData = $cartItems->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ];
        })->toArray();

        $validation = $this->stockService->validateCartStock($cartData);

        if (!$validation['valid']) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Ada masalah dengan stok produk di keranjang Anda')
                ->with('stock_errors', $validation['errors'])
                ->with('suggestions', $validation['suggestions']);
        }

        $total = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return view('checkout.index', compact('cartItems', 'total', 'totalItems'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:transfer,cod',
            'pickup_method' => 'required|in:delivery,pickup',
            'shipping_address' => 'required_if:pickup_method,delivery|string|max:500|nullable',
            'note' => 'nullable|string|max:500'
        ]);

        try {
            // Start transaction with locking to prevent race condition
            return DB::transaction(function () use ($request) {
                // Get cart items with LOCK FOR UPDATE to prevent concurrent access
                $cartItems = Cart::with(['product'])
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->get();

                if ($cartItems->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keranjang Anda kosong'
                    ], 400);
                }

                // Lock all product stocks to prevent race condition
                $productIds = $cartItems->pluck('product_id')->toArray();
                $stocks = \App\Models\Stock::whereIn('product_id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('product_id');

                // Final stock validation with locked data
                $stockErrors = [];
                foreach ($cartItems as $cartItem) {
                    $stock = $stocks->get($cartItem->product_id);
                    $availableStock = $stock ? $stock->quantity : 0;

                    if ($availableStock < $cartItem->quantity) {
                        if ($availableStock == 0) {
                            $stockErrors[] = "Produk {$cartItem->product->name} sudah habis";
                        } else {
                            $stockErrors[] = "Produk {$cartItem->product->name} hanya tersisa {$availableStock} unit, Anda memesan {$cartItem->quantity} unit";
                        }
                    }
                }

                if (!empty($stockErrors)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi stok gagal',
                        'errors' => $stockErrors
                    ], 400);
                }

                // Generate order code
                $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4)) . '-' . str_pad(\Illuminate\Support\Facades\Cache::increment('order_code_seq_' . date('Ymd')), 4, '0', STR_PAD_LEFT);

                // Setup checkout logic for BOPIS
                $isPickup = $request->pickup_method === 'pickup';
                $shippingAddress = $isPickup ? 'Toko Pojok Jepara - Jl. Raya Pojok No. 123, Jepara' : $request->shipping_address;
                $status = $isPickup ? 'menunggu_diambil' : 'pending';
                $pickupDeadline = $isPickup ? now()->addHours(24) : null;

                // Calculate estimated ready time for BOPIS orders
                $estimatedReadyAt = null;
                if ($isPickup) {
                    $estimatedMinutes = Order::calculateEstimatedMinutes($cartItems);
                    $estimatedReadyAt = now()->addMinutes($estimatedMinutes);
                }

                // Create order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_code' => $orderCode,
                    'status' => $status,
                    'total_amount' => $cartItems->sum('subtotal'),
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'shipping_address' => $shippingAddress,
                    'pickup_method' => $request->pickup_method,
                    'pickup_deadline' => $pickupDeadline,
                    'estimated_ready_at' => $estimatedReadyAt,
                    'note' => $request->note
                ]);

                // Create order items
                $orderItemsData = [];
                foreach ($cartItems as $cartItem) {
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price_used,
                        'subtotal' => $cartItem->subtotal
                    ]);

                    $orderItemsData[] = [
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'order_code' => $orderCode
                    ];
                }

                // Update stock (reduce quantities) - already uses locking internally
                $this->stockService->processOrderStock($orderItemsData);

                // Clear cart
                Cart::where('user_id', Auth::id())->delete();

                // Notify admin about the new order
                app(\App\Services\NotificationService::class)->notifyNewOrder($order);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat',
                    'order_code' => $orderCode,
                    'redirect_url' => route('pesanan.show', $orderCode)
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateStock(Request $request)
    {
        $cartItems = Cart::with(['product.stock'])
            ->where('user_id', Auth::id())
            ->get();

        $cartData = $cartItems->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ];
        })->toArray();

        $validation = $this->stockService->validateCartStock($cartData);

        return response()->json([
            'valid' => $validation['valid'],
            'errors' => $validation['errors'],
            'suggestions' => $validation['suggestions']
        ]);
    }
}