<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StockService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        // Stock statistics
        $stockStats = $this->stockService->getStockStatistics();
        
        // Today's orders
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Today's revenue
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        
        // Critical stock products
        $criticalProducts = $this->stockService->getCriticalStockProducts();
        
        // Out of stock products
        $outOfStockProducts = $this->stockService->getOutOfStockProducts();
        
        // Recent stock movements
        $recentMovements = $this->stockService->getRecentStockMovements(10);
        
        // Weekly sales chart data
        $weeklyData = $this->getWeeklyChartData();
        
        // Top selling products this month
        $topProducts = $this->getTopSellingProducts();
        
        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stockStats',
            'todayOrders',
            'todayRevenue',
            'criticalProducts',
            'outOfStockProducts',
            'recentMovements',
            'weeklyData',
            'topProducts',
            'recentOrders'
        ));
    }

    private function getWeeklyChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orders = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->count();
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            
            $data[] = [
                'date' => $date->format('d/m'),
                'orders' => $orders,
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }

    private function getTopSellingProducts()
    {
        return OrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->whereHas('order', function($query) {
                $query->whereMonth('created_at', now()->month)
                      ->where('status', '!=', 'cancelled');
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
    }
}