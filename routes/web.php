<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StockAlertController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    $featuredProducts = \Illuminate\Support\Facades\Cache::remember('featured_products', 3600, function() {
        return \App\Models\Product::with(['stock', 'category', 'cluster'])
            ->whereHas('stock', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();
    });
    
    $categories = \App\Models\Category::withCount('products')->orderBy('name')->get();
    
    return view('welcome', compact('featuredProducts', 'categories'));
})->name('home');

// Product Routes (Public)
Route::prefix('produk')->name('produk.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
});

// Category Routes (Public)
Route::get('/kategori/{category:slug}', [ProductController::class, 'byCategory'])->name('kategori.show');

// Authentication Routes (Laravel Breeze will handle this)
require __DIR__.'/auth.php';

// Customer Routes (Authenticated)
Route::middleware('auth')->group(function () {
    
    // Cart Routes
    Route::prefix('keranjang')->name('keranjang.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/tambah', [CartController::class, 'add'])->name('add');
        Route::patch('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/hapus', [CartController::class, 'remove'])->name('remove');
        Route::delete('/kosongkan', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
    });

    // Checkout Routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/proses', [CheckoutController::class, 'process'])->name('process');
        Route::post('/validasi-stok', [CheckoutController::class, 'validateStock'])->name('validate-stock');
    });

    // Order Routes
    Route::prefix('pesanan')->name('pesanan.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{orderCode}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{orderCode}/batal', [OrderController::class, 'cancel'])->name('cancel');
        Route::patch('/{orderCode}/selesai', [OrderController::class, 'complete'])->name('complete');
    });

    // Stock Alert Subscription
    Route::post('/subscribe-stok', [StockAlertController::class, 'subscribe'])->name('stock-alert.subscribe');
    Route::delete('/unsubscribe-stok', [StockAlertController::class, 'unsubscribe'])->name('stock-alert.unsubscribe');

    // Notifications
    Route::prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/count', [NotificationController::class, 'count'])->name('count');
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Product Management
    Route::resource('produk', \App\Http\Controllers\Admin\ProductController::class);

    // Stock Management
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::post('/update', [StockController::class, 'update'])->name('update');
        Route::post('/restock', [StockController::class, 'restock'])->name('restock');
        Route::post('/adjust', [StockController::class, 'adjust'])->name('adjust');
        Route::get('/histori', [StockController::class, 'history'])->name('history');
        Route::get('/export', [StockController::class, 'export'])->name('export');
    });

    // Order Management
    Route::prefix('pesanan')->name('pesanan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/picked-up', [\App\Http\Controllers\Admin\OrderController::class, 'markPickedUp'])->name('picked-up');
        Route::patch('/{order}/update-estimation', [\App\Http\Controllers\Admin\OrderController::class, 'updateEstimation'])->name('update-estimation');
        Route::post('/{order}/mark-ready', [\App\Http\Controllers\Admin\OrderController::class, 'markReady'])->name('mark-ready');
    });

    // Clustering Management
    Route::prefix('clustering')->name('clustering.')->group(function () {
        Route::get('/', [ClusteringController::class, 'index'])->name('index');
        Route::post('/run', [ClusteringController::class, 'run'])->name('run');
        Route::get('/cluster/{cluster}', [ClusteringController::class, 'details'])->name('details');
        Route::get('/export', [ClusteringController::class, 'export'])->name('export');
    });

    // Reports
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/penjualan', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('/stok', [\App\Http\Controllers\Admin\ReportController::class, 'stock'])->name('stock');
        Route::get('/export/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // Notifications
    Route::get('/notifikasi', [NotificationController::class, 'admin'])->name('notifications');
});


// API Routes for AJAX calls
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/products/{product}/stock-status', function(\App\Models\Product $product) {
        return response()->json([
            'stock' => $product->stock ? $product->stock->quantity : 0,
            'status' => $product->stock_status
        ]);
    })->name('product.stock-status');
    
    Route::get('/cart/total', function() {
        $total = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
        return response()->json(['total' => $total]);
    })->name('cart.total');
});
