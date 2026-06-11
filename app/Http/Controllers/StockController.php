<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['stock', 'category', 'cluster']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by stock status
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'available':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 10);
                    });
                    break;
                case 'critical':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    });
                    break;
                case 'out_of_stock':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '<=', 0);
                    });
                    break;
                case 'high_priority':
                    $query->whereHas('cluster', function($q) {
                        $q->where('priority_level', 'high');
                    });
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        
        // Statistics
        $stats = $this->stockService->getStockStatistics();
        
        // Get categories for filter
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.stok.index', compact('products', 'stats', 'categories'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $success = $this->stockService->updateStock(
            $product,
            $request->quantity,
            'in',
            $request->note ?: 'Restock manual oleh admin'
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Stok {$product->name} berhasil ditambahkan sebanyak {$request->quantity} unit"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan stok'
        ], 500);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $success = $this->stockService->updateStock(
            $product,
            $request->quantity,
            $request->type,
            $request->note ?: 'Update stok manual oleh admin'
        );

        if ($success) {
            $message = match($request->type) {
                'in' => "Stok {$product->name} berhasil ditambahkan sebanyak {$request->quantity} unit",
                'out' => "Stok {$product->name} berhasil dikurangi sebanyak {$request->quantity} unit",
                'adjustment' => "Stok {$product->name} berhasil disesuaikan menjadi {$request->quantity} unit",
            };
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate stok'
        ], 500);
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'note' => 'required|string|max:255'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $success = $this->stockService->updateStock(
            $product,
            $request->quantity,
            'adjustment',
            $request->note
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Stok {$product->name} berhasil disesuaikan menjadi {$request->quantity} unit"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyesuaikan stok'
        ], 500);
    }

    public function history(Request $request)
    {
        $query = StockHistory::with(['product', 'user']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // Get products for filter dropdown
        $products = Product::orderBy('name')->get();

        return view('admin.stok.history', compact('histories', 'products'));
    }

    public function export(Request $request)
    {
        $query = Product::with(['stock', 'category']);

        // Apply same filters as index
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'critical':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    });
                    break;
                case 'out_of_stock':
                    $query->whereHas('stock', function($q) {
                        $q->where('quantity', '<=', 0);
                    });
                    break;
            }
        }

        $products = $query->orderBy('name')->get();

        $filename = 'stock_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['Nama Produk', 'Kategori', 'Stok', 'Status', 'Harga', 'Harga Grosir']);
            
            foreach ($products as $product) {
                $stock = $product->stock ? $product->stock->quantity : 0;
                $status = $product->stock_status['status'];
                
                fputcsv($file, [
                    $product->name,
                    $product->category->name,
                    $stock,
                    $status,
                    $product->price,
                    $product->wholesale_price
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}