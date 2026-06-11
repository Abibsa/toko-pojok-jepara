<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCluster;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KMeansService
{
    private int $k = 3;
    private int $maxIterations = 100;
    private float $tolerance = 0.001;

    public function runClustering(): array
    {
        $products = $this->getProductsWithScores();
        
        if ($products->count() < $this->k) {
            throw new \Exception("Tidak cukup produk untuk clustering (minimal {$this->k} produk)");
        }

        // Normalize scores
        $normalizedData = $this->normalizeScores($products);
        
        // Run K-Means
        $clusters = $this->kMeans($normalizedData);
        
        // Determine priority levels
        $clusterPriorities = $this->determinePriorityLevels($clusters, $normalizedData);
        
        // Save results
        $this->saveClusterResults($clusters, $clusterPriorities, $products);
        
        return [
            'total_products' => $products->count(),
            'clusters' => $this->getClusterSummary($clusters, $clusterPriorities),
            'clustered_at' => now()
        ];
    }

    private function getProductsWithScores()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        return Product::with(['orderItems' => function($query) use ($thirtyDaysAgo) {
            $query->whereHas('order', function($orderQuery) use ($thirtyDaysAgo) {
                $orderQuery->where('created_at', '>=', $thirtyDaysAgo)
                          ->where('status', 'completed');
            });
        }])->get()->map(function($product) {
            $orderItems = $product->orderItems;
            
            // Calculate frequency score (unique transactions)
            $frequencyScore = $orderItems->groupBy('order_id')->count();
            
            // Calculate quantity score (total units sold)
            $quantityScore = $orderItems->sum('quantity');
            
            // Calculate urgency score (average days between purchases)
            $urgencyScore = $this->calculateUrgencyScore($orderItems);
            
            $product->frequency_score = $frequencyScore;
            $product->quantity_score = $quantityScore;
            $product->urgency_score = $urgencyScore;
            
            return $product;
        })->filter(function($product) {
            // Only include products with at least one transaction
            return $product->frequency_score > 0;
        });
    }

    private function calculateUrgencyScore($orderItems)
    {
        if ($orderItems->count() <= 1) {
            return 30; // Default high urgency for single purchases
        }

        $dates = $orderItems->map(function($item) {
            return Carbon::parse($item->order->created_at);
        })->sort();

        $intervals = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $intervals[] = $dates[$i]->diffInDays($dates[$i-1]);
        }

        return collect($intervals)->average() ?: 30;
    }

    private function normalizeScores($products)
    {
        $frequencyScores = $products->pluck('frequency_score');
        $quantityScores = $products->pluck('quantity_score');
        $urgencyScores = $products->pluck('urgency_score');

        $minFreq = $frequencyScores->min();
        $maxFreq = $frequencyScores->max();
        $minQty = $quantityScores->min();
        $maxQty = $quantityScores->max();
        $minUrg = $urgencyScores->min();
        $maxUrg = $urgencyScores->max();

        return $products->map(function($product) use ($minFreq, $maxFreq, $minQty, $maxQty, $minUrg, $maxUrg) {
            return [
                'id' => $product->id,
                'frequency' => $this->normalize($product->frequency_score, $minFreq, $maxFreq),
                'quantity' => $this->normalize($product->quantity_score, $minQty, $maxQty),
                'urgency' => $this->normalize(1 / max($product->urgency_score, 1), 1 / max($maxUrg, 1), 1 / max($minUrg, 1)), // Invert urgency
                'original_frequency' => $product->frequency_score,
                'original_quantity' => $product->quantity_score,
                'original_urgency' => $product->urgency_score
            ];
        })->toArray();
    }

    private function normalize($value, $min, $max)
    {
        if ($max == $min) return 0.5;
        return ($value - $min) / ($max - $min);
    }

    private function kMeans(array $data)
    {
        $n = count($data);
        
        // Initialize centroids randomly
        $centroids = $this->initializeCentroids($data);
        $clusters = array_fill(0, $this->k, []);
        
        for ($iteration = 0; $iteration < $this->maxIterations; $iteration++) {
            $newClusters = array_fill(0, $this->k, []);
            
            // Assign points to nearest centroid
            foreach ($data as $point) {
                $nearestCentroid = $this->findNearestCentroid($point, $centroids);
                $newClusters[$nearestCentroid][] = $point;
            }
            
            // Check for convergence
            if ($this->clustersEqual($clusters, $newClusters)) {
                break;
            }
            
            $clusters = $newClusters;
            
            // Update centroids
            $newCentroids = [];
            for ($i = 0; $i < $this->k; $i++) {
                if (empty($clusters[$i])) {
                    $newCentroids[$i] = $centroids[$i]; // Keep old centroid if cluster is empty
                } else {
                    $newCentroids[$i] = $this->calculateCentroid($clusters[$i]);
                }
            }
            $centroids = $newCentroids;
        }
        
        return $clusters;
    }

    private function initializeCentroids(array $data)
    {
        $centroids = [];
        $indices = array_rand($data, min($this->k, count($data)));
        
        if (!is_array($indices)) {
            $indices = [$indices];
        }
        
        foreach ($indices as $index) {
            $centroids[] = [
                'frequency' => $data[$index]['frequency'],
                'quantity' => $data[$index]['quantity'],
                'urgency' => $data[$index]['urgency']
            ];
        }
        
        // Fill remaining centroids if needed
        while (count($centroids) < $this->k) {
            $centroids[] = [
                'frequency' => rand(0, 100) / 100,
                'quantity' => rand(0, 100) / 100,
                'urgency' => rand(0, 100) / 100
            ];
        }
        
        return $centroids;
    }

    private function findNearestCentroid(array $point, array $centroids)
    {
        $minDistance = PHP_FLOAT_MAX;
        $nearestCentroid = 0;
        
        foreach ($centroids as $i => $centroid) {
            $distance = $this->euclideanDistance($point, $centroid);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestCentroid = $i;
            }
        }
        
        return $nearestCentroid;
    }

    private function euclideanDistance(array $point1, array $point2)
    {
        $sum = 0;
        $sum += pow($point1['frequency'] - $point2['frequency'], 2);
        $sum += pow($point1['quantity'] - $point2['quantity'], 2);
        $sum += pow($point1['urgency'] - $point2['urgency'], 2);
        
        return sqrt($sum);
    }

    private function calculateCentroid(array $cluster)
    {
        $n = count($cluster);
        $sumFreq = array_sum(array_column($cluster, 'frequency'));
        $sumQty = array_sum(array_column($cluster, 'quantity'));
        $sumUrg = array_sum(array_column($cluster, 'urgency'));
        
        return [
            'frequency' => $sumFreq / $n,
            'quantity' => $sumQty / $n,
            'urgency' => $sumUrg / $n
        ];
    }

    private function clustersEqual(array $clusters1, array $clusters2)
    {
        if (count($clusters1) !== count($clusters2)) {
            return false;
        }
        
        for ($i = 0; $i < count($clusters1); $i++) {
            if (count($clusters1[$i]) !== count($clusters2[$i])) {
                return false;
            }
            
            $ids1 = array_column($clusters1[$i], 'id');
            $ids2 = array_column($clusters2[$i], 'id');
            sort($ids1);
            sort($ids2);
            
            if ($ids1 !== $ids2) {
                return false;
            }
        }
        
        return true;
    }

    private function determinePriorityLevels(array $clusters, array $data)
    {
        $clusterScores = [];
        
        foreach ($clusters as $i => $cluster) {
            if (empty($cluster)) {
                $clusterScores[$i] = 0;
                continue;
            }
            
            $totalScore = 0;
            foreach ($cluster as $point) {
                $totalScore += $point['frequency'] + $point['quantity'] + $point['urgency'];
            }
            $clusterScores[$i] = $totalScore / count($cluster);
        }
        
        arsort($clusterScores);
        $sortedClusters = array_keys($clusterScores);
        
        return [
            $sortedClusters[0] => 'high',
            $sortedClusters[1] => 'medium',
            $sortedClusters[2] => 'low'
        ];
    }

    private function saveClusterResults(array $clusters, array $priorities, $products)
    {
        DB::transaction(function() use ($clusters, $priorities, $products) {
            // Clear existing clusters
            ProductCluster::truncate();
            
            foreach ($clusters as $clusterIndex => $cluster) {
                $priority = $priorities[$clusterIndex];
                
                foreach ($cluster as $point) {
                    ProductCluster::create([
                        'product_id' => $point['id'],
                        'cluster' => $clusterIndex + 1,
                        'priority_level' => $priority,
                        'frequency_score' => $point['original_frequency'],
                        'quantity_score' => $point['original_quantity'],
                        'urgency_score' => $point['original_urgency'],
                        'last_clustered_at' => now()
                    ]);
                }
            }
        });
    }

    private function getClusterSummary(array $clusters, array $priorities)
    {
        $summary = [];
        
        foreach ($clusters as $i => $cluster) {
            $summary[] = [
                'cluster' => $i + 1,
                'priority' => $priorities[$i],
                'product_count' => count($cluster),
                'products' => array_column($cluster, 'id')
            ];
        }
        
        return $summary;
    }

    public function getClusterResults()
    {
        return ProductCluster::with('product')
            ->orderBy('cluster')
            ->orderBy('priority_level')
            ->get()
            ->groupBy('cluster');
    }
}