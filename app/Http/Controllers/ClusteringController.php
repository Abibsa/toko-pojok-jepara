<?php

namespace App\Http\Controllers;

use App\Services\KMeansService;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    protected $kmeansService;

    public function __construct(KMeansService $kmeansService)
    {
        $this->kmeansService = $kmeansService;
    }

    public function index()
    {
        $clusters = $this->kmeansService->getClusterResults();
        
        // Get last clustering date
        $lastClustered = \App\Models\ProductCluster::max('last_clustered_at');
        
        // Calculate cluster statistics
        $clusterStats = [];
        foreach ($clusters as $clusterNumber => $products) {
            $clusterStats[$clusterNumber] = [
                'count' => $products->count(),
                'priority' => $products->first()->priority_level ?? 'unknown',
                'avg_frequency' => $products->avg('frequency_score'),
                'avg_quantity' => $products->avg('quantity_score'),
                'avg_urgency' => $products->avg('urgency_score')
            ];
        }

        return view('admin.clustering.index', compact('clusters', 'lastClustered', 'clusterStats'));
    }

    public function run(Request $request)
    {
        try {
            $result = $this->kmeansService->runClustering();
            
            return response()->json([
                'success' => true,
                'message' => 'K-Means clustering berhasil dijalankan',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan clustering: ' . $e->getMessage()
            ], 500);
        }
    }

    public function details($cluster)
    {
        $products = \App\Models\ProductCluster::with(['product.stock', 'product.category'])
            ->where('cluster', $cluster)
            ->orderBy('priority_level')
            ->orderBy('frequency_score', 'desc')
            ->get();

        if ($products->isEmpty()) {
            abort(404, 'Cluster tidak ditemukan');
        }

        $clusterInfo = [
            'number' => $cluster,
            'priority' => $products->first()->priority_level,
            'count' => $products->count(),
            'avg_frequency' => $products->avg('frequency_score'),
            'avg_quantity' => $products->avg('quantity_score'),
            'avg_urgency' => $products->avg('urgency_score')
        ];

        return view('admin.clustering.details', compact('products', 'clusterInfo'));
    }

    public function export()
    {
        $clusters = $this->kmeansService->getClusterResults();
        
        $filename = 'clustering_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($clusters) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Cluster', 
                'Prioritas', 
                'Nama Produk', 
                'Kategori', 
                'Stok', 
                'Frequency Score', 
                'Quantity Score', 
                'Urgency Score'
            ]);
            
            foreach ($clusters as $clusterNumber => $products) {
                foreach ($products as $productCluster) {
                    $product = $productCluster->product;
                    $stock = $product->stock ? $product->stock->quantity : 0;
                    
                    fputcsv($file, [
                        $clusterNumber,
                        ucfirst($productCluster->priority_level),
                        $product->name,
                        $product->category->name,
                        $stock,
                        $productCluster->frequency_score,
                        $productCluster->quantity_score,
                        $productCluster->urgency_score
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}