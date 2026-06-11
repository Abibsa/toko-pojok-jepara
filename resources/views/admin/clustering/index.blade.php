@extends('layouts.admin')

@section('title', 'K-Means Clustering')
@section('subtitle', 'Analisis prioritas stok produk dengan algoritma K-Means')

@section('content')

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-8">
        <div>
            @if($lastClustered)
                <p class="text-sm text-gray-400">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Terakhir dijalankan: {{ \Carbon\Carbon::parse($lastClustered)->diffForHumans() }}
                    </span>
                </p>
            @else
                <p class="text-sm text-yellow-400">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Clustering belum pernah dijalankan
                    </span>
                </p>
            @endif
        </div>
        <button onclick="runClustering()" id="runClusteringBtn"
                class="px-6 py-3 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-red-500/30 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Jalankan Clustering
        </button>
    </div>

    <!-- Cluster Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($clusterStats as $clusterNumber => $stats)
            @php
                $priorityColors = [
                    'high' => ['bg' => 'from-red-600 to-rose-500', 'text' => 'text-red-400', 'shadow' => 'shadow-red-500/30', 'border' => 'border-red-500/30'],
                    'medium' => ['bg' => 'from-yellow-600 to-orange-500', 'text' => 'text-yellow-400', 'shadow' => 'shadow-yellow-500/30', 'border' => 'border-yellow-500/30'],
                    'low' => ['bg' => 'from-gray-600 to-gray-500', 'text' => 'text-gray-400', 'shadow' => 'shadow-gray-500/30', 'border' => 'border-gray-500/30'],
                ];
                $colors = $priorityColors[$stats['priority']] ?? $priorityColors['low'];
            @endphp
            
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 hover:scale-105 transition-all duration-300 animate-fade-in" style="animation-delay: {{ $clusterNumber * 0.1 }}s;">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Cluster {{ $clusterNumber }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['count'] }} Produk</p>
                    </div>
                    <div class="p-3 rounded-xl bg-gradient-to-r {{ $colors['bg'] }} shadow-lg {{ $colors['shadow'] }}">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-400">Prioritas</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colors['text'] }} bg-white/5 border {{ $colors['border'] }} uppercase">
                            {{ $stats['priority'] }}
                        </span>
                    </div>
                    
                    <div class="pt-3 border-t border-white/10 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Avg Frequency</span>
                            <span class="font-bold text-white">{{ number_format($stats['avg_frequency'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Avg Quantity</span>
                            <span class="font-bold text-white">{{ number_format($stats['avg_quantity'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Avg Urgency</span>
                            <span class="font-bold text-white">{{ number_format($stats['avg_urgency'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.clustering.details', $clusterNumber) }}" 
                   class="mt-4 block w-full px-4 py-2 text-center text-sm font-semibold text-white bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300">
                    Lihat Detail
                </a>
            </div>
        @endforeach
    </div>

    <!-- Cluster Details -->
    @foreach($clusters as $clusterNumber => $products)
        @php
            $priority = $products->first()->priority_level ?? 'low';
            $priorityColors = [
                'high' => ['bg' => 'from-red-600 to-rose-500', 'text' => 'text-red-400', 'shadow' => 'shadow-red-500/30', 'border' => 'border-red-500/30', 'glow' => 'shadow-red-500/20'],
                'medium' => ['bg' => 'from-yellow-600 to-orange-500', 'text' => 'text-yellow-400', 'shadow' => 'shadow-yellow-500/30', 'border' => 'border-yellow-500/30', 'glow' => 'shadow-yellow-500/20'],
                'low' => ['bg' => 'from-gray-600 to-gray-500', 'text' => 'text-gray-400', 'shadow' => 'shadow-gray-500/30', 'border' => 'border-gray-500/30', 'glow' => 'shadow-gray-500/20'],
            ];
            $colors = $priorityColors[$priority] ?? $priorityColors['low'];
        @endphp

        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden mb-6 animate-fade-in">
            <div class="p-6 border-b border-white/10 bg-gradient-to-r {{ $colors['bg'] }}/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-xl bg-gradient-to-r {{ $colors['bg'] }} shadow-lg {{ $colors['shadow'] }}">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">Cluster {{ $clusterNumber }}</h3>
                            <p class="text-sm text-gray-300 mt-1">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colors['text'] }} bg-white/10 border {{ $colors['border'] }} uppercase">
                                    {{ $priority }} Priority
                                </span>
                                <span class="ml-3 text-gray-400">{{ $products->count() }} Produk</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.clustering.details', $clusterNumber) }}" 
                       class="px-4 py-2 text-sm font-semibold text-white bg-white/10 border border-white/20 rounded-xl hover:bg-white/20 transition-all duration-300">
                        Detail Lengkap →
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Frequency</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Urgency</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($products->take(5) as $productCluster)
                            <tr class="hover:bg-white/5 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $productCluster->product->image_url }}" 
                                             alt="{{ $productCluster->product->name }}" 
                                             class="w-10 h-10 object-cover rounded-lg border border-white/10">
                                        <div>
                                            <div class="text-sm font-semibold text-white">{{ $productCluster->product->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $productCluster->product->formatted_price }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $productCluster->product->category->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-lg font-black {{ ($productCluster->product->stock->quantity ?? 0) <= 0 ? 'text-red-400' : (($productCluster->product->stock->quantity ?? 0) <= 10 ? 'text-yellow-400' : 'text-green-400') }}">
                                        {{ $productCluster->product->stock->quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->frequency_score * 10, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400">{{ number_format($productCluster->frequency_score, 2) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->quantity_score * 10, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400">{{ number_format($productCluster->quantity_score, 2) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->urgency_score * 10, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400">{{ number_format($productCluster->urgency_score, 2) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($products->count() > 5)
                <div class="p-4 border-t border-white/10 text-center">
                    <a href="{{ route('admin.clustering.details', $clusterNumber) }}" 
                       class="text-sm font-semibold {{ $colors['text'] }} hover:underline">
                        Lihat {{ $products->count() - 5 }} produk lainnya →
                    </a>
                </div>
            @endif
        </div>
    @endforeach

    @if($clusters->isEmpty())
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-12 text-center">
            <svg class="w-20 h-20 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-white mb-2">Belum Ada Data Clustering</h3>
            <p class="text-gray-400 mb-6">Klik tombol "Jalankan Clustering" untuk memulai analisis</p>
            <button onclick="runClustering()" 
                    class="px-6 py-3 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-red-500/30">
                Jalankan Clustering Sekarang
            </button>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    function runClustering() {
        const btn = document.getElementById('runClusteringBtn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Memproses...</span>
        `;
        
        fetch('{{ route("admin.clustering.run") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menjalankan clustering', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-4 p-4 rounded-2xl shadow-2xl z-50 backdrop-blur-xl border animate-slide-up ${
            type === 'success' 
                ? 'bg-green-500/10 text-green-400 border-green-500/30 shadow-green-500/20' 
                : 'bg-red-500/10 text-red-400 border-red-500/30 shadow-red-500/20'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>
@endpush
