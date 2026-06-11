<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockAlertController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $alert = $this->notificationService->subscribeToStockAlert($product, Auth::user());

        if ($alert->wasRecentlyCreated) {
            return response()->json([
                'success' => true,
                'message' => "Anda akan diberitahu ketika {$product->name} tersedia kembali"
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => "Anda sudah masuk dalam daftar antrean notifikasi untuk produk ini!"
            ]);
        }
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $this->notificationService->unsubscribeFromStockAlert($product, Auth::user());

        return response()->json([
            'success' => true,
            'message' => "Anda tidak akan lagi diberitahu tentang {$product->name}"
        ]);
    }
}