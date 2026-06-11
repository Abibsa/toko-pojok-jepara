<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with(['orderItems.product'])
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        return view('admin.laporan.sales', compact('orders', 'totalRevenue', 'totalOrders', 'startDate', 'endDate'));
    }

    public function stock()
    {
        $stocks = Stock::with('product')->get();
        
        $criticalStock = $stocks->filter(function($stock) {
            return $stock->quantity > 0 && $stock->quantity <= 10;
        });

        $outOfStock = $stocks->filter(function($stock) {
            return $stock->quantity == 0;
        });

        return view('admin.laporan.stock', compact('stocks', 'criticalStock', 'outOfStock'));
    }

    public function export($type)
    {
        // Export functionality can be implemented here
        return back()->with('info', 'Fitur export sedang dalam pengembangan');
    }
}
