<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index()
    {
        $cartItems = Cart::with(['product.stock', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return view('keranjang.index', compact('cartItems', 'total', 'totalItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::with('stock')->findOrFail($request->product_id);
        
        // Check stock availability
        $availableStock = $product->stock ? $product->stock->quantity : 0;
        
        // Check existing cart quantity
        $existingCart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();
        
        $currentCartQuantity = $existingCart ? $existingCart->quantity : 0;
        $totalRequestedQuantity = $currentCartQuantity + $request->quantity;
        
        if ($totalRequestedQuantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Tersedia: {$availableStock}, di keranjang: {$currentCartQuantity}"
            ], 400);
        }

        if ($existingCart) {
            $existingCart->increment('quantity', $request->quantity);
            // Update price snapshot if price changed
            $currentPrice = $request->quantity >= 5 ? $product->wholesale_price : $product->price;
            if (!$existingCart->price_snapshot) {
                $existingCart->update(['price_snapshot' => $currentPrice]);
            }
        } else {
            $priceSnapshot = $request->quantity >= 5 ? $product->wholesale_price : $product->price;
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price_snapshot' => $priceSnapshot
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $product = Product::with('stock')->findOrFail($cart->product_id);
        $availableStock = $product->stock ? $product->stock->quantity : 0;

        if ($request->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Tersedia: {$availableStock}"
            ], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        // Recalculate totals
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        $total = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'subtotal' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
            'total' => 'Rp ' . number_format($total, 0, ',', '.'),
            'total_items' => $totalItems
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id'
        ]);

        $cart = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cart->delete();

        // Recalculate totals
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        $total = $cartItems->sum('subtotal');
        $totalItems = $cartItems->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'total' => 'Rp ' . number_format($total, 0, ',', '.'),
            'total_items' => $totalItems
        ]);
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }

    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json([
            'count' => $count
        ]);
    }
}