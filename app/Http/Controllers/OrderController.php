<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $orders = Order::with(['orderItems.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pesanan.index', compact('orders'));
    }

    public function show($orderCode)
    {
        $order = Order::with(['orderItems.product', 'user'])
            ->where('order_code', $orderCode)
            ->firstOrFail();

        // Check if user can view this order
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini');
        }

        return view('pesanan.show', compact('order'));
    }

    public function cancel(Request $request, $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($order->status, ['pending', 'confirmed', 'menunggu_diambil'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan pada status ini'
            ], 400);
        }

        try {
            // Restore stock for pending, confirmed, and BOPIS orders
            if (in_array($order->status, ['pending', 'confirmed', 'menunggu_diambil'])) {
                $orderItemsData = $order->orderItems->map(function($item) use ($order) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity
                    ];
                })->toArray();

                $this->stockService->restoreOrderStock($orderItemsData, $order->order_code);
            }

            $order->update([
                'status' => 'cancelled',
                'note' => ($order->note ? $order->note . ' | ' : '') . 'Dibatalkan oleh customer pada ' . now()->format('d/m/Y H:i')
            ]);

            // Notify admin about the cancellation
            app(\App\Services\NotificationService::class)->notifyOrderCancelled($order);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pesanan'
            ], 500);
        }
    }

    public function complete(Request $request, $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan yang sedang dikirim yang dapat diselesaikan'
            ], 400);
        }

        try {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'note' => ($order->note ? $order->note . ' | ' : '') . 'Diselesaikan oleh customer pada ' . now()->format('d/m/Y H:i')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diselesaikan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan pesanan'
            ], 500);
        }
    }
}