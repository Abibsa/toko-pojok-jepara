<?php

namespace App\Console\Commands;

use App\Services\KMeansService;
use Illuminate\Console\Command;

class RunKMeansClustering extends Command
{
    protected $signature = 'kmeans:cluster {--force : Force clustering even if recently run}';
    protected $description = 'Run K-Means clustering algorithm to categorize products by priority';

    protected $kmeansService;

    public function __construct(KMeansService $kmeansService)
    {
        parent::__construct();
        $this->kmeansService = $kmeansService;
    }

    public function handle()
    {
        $this->info('Starting K-Means clustering...');
        
        // Check if clustering was recently run (unless forced)
        if (!$this->option('force')) {
            $lastRun = \App\Models\ProductCluster::max('last_clustered_at');
            if ($lastRun && now()->diffInHours($lastRun) < 24) {
                $this->warn('Clustering was run less than 24 hours ago. Use --force to override.');
                return 1;
            }
        }

        try {
            $result = $this->kmeansService->runClustering();
            
            $this->info("✅ Clustering completed successfully!");
            $this->info("📊 Total products clustered: {$result['total_products']}");
            
            foreach ($result['clusters'] as $cluster) {
                $priority = ucfirst($cluster['priority']);
                $this->line("   Cluster {$cluster['cluster']} ({$priority}): {$cluster['product_count']} products");
            }
            
            $this->info("🕒 Clustered at: {$result['clustered_at']->format('Y-m-d H:i:s')}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Clustering failed: {$e->getMessage()}");
            return 1;
        }
    }
}