@extends('layouts.admin')

@section('title', 'Detail Cluster ' . $clusterInfo['number'])
@section('subtitle', 'Analisis detail produk dalam cluster')

@section('content')

    @php
        $priorityColors = [
            'high' => ['bg' => 'from-red-600 to-rose-500', 'text' => 'text-red-400', 'shadow' => 'shadow-red-500/30', 'border' => 'border-red-500/30'],
            'medium' => ['bg' => 'from-yellow-600 to-orange-500', 'text' => 'text-yellow-400', 'shadow' => 'shadow-yellow-500/30', 'border' => 'border-yellow-500/30'],
            'low' => ['bg' => 'from-gray-600 to-gray-500', 'text' => 'text-gray-400', 'shadow' => 'shadow-gray-500/30', 'border' => 'border-gray-500/30'],
        ];
        $colors = $priorityColors[$clusterInfo['priority']] ?? $priorityColors['low'];
    @endphp

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.clustering.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Clustering
        </a>
    </div>

    <!-- Cluster Info Card -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 mb-8 animate-fade-in">
        <div class="flex items-center gap-6">
            <div class="p-6 rounded-2xl bg-gradient-to-r {{ $colors['bg'] }} shadow-2xl {{ $colors['shadow'] }}">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black text-white mb-2">Cluster {{ $clusterInfo['number'] }}</h2>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ $colors['text'] }} bg-white/10 border {{ $colors['border'] }} uppercase">
                        {{ $clusterInfo['priority'] }} Priority
                    </span>
                    <span class="text-gray-400">{{ $clusterInfo['count'] }} Produk</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-8 border-t border-white/10">
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-400 mb-2">Average Frequency Score</p>
                <p class="text-3xl font-black {{ $colors['text'] }}">{{ number_format($clusterInfo['avg_frequency'], 2) }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-400 mb-2">Average Quantity Score</p>
                <p class="text-3xl font-black {{ $colors['text'] }}">{{ number_format($clusterInfo['avg_quantity'], 2) }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-400 mb-2">Average Urgency Score</p>
                <p class="text-3xl font-black {{ $colors['text'] }}">{{ number_format($clusterInfo['avg_urgency'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Daftar Produk</h3>
            <p class="text-sm text-gray-400 mt-1">Semua produk dalam cluster ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Frequency</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Urgency</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($products as $productCluster)
                        <tr class="hover:bg-white/5 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $productCluster->product->image_url }}" 
                                         alt="{{ $productCluster->product->name }}" 
                                         class="w-12 h-12 object-cover rounded-xl border border-white/10">
                                    <div>
                                        <div class="text-sm font-semibold text-white">{{ $productCluster->product->name }}</div>
                                        <div class="text-xs text-gray-400">SKU: {{ $productCluster->product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $productCluster->product->category->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-2xl font-black {{ ($productCluster->product->stock->quantity ?? 0) <= 0 ? 'text-red-400' : (($productCluster->product->stock->quantity ?? 0) <= 10 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $productCluster->product->stock->quantity ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-white">{{ $productCluster->product->formatted_price }}</div>
                                <div class="text-xs text-gray-400">Grosir: {{ $productCluster->product->formatted_wholesale_price }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->frequency_score * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold {{ $colors['text'] }}">{{ number_format($productCluster->frequency_score, 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->quantity_score * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold {{ $colors['text'] }}">{{ number_format($productCluster->quantity_score, 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $colors['bg'] }}" style="width: {{ min($productCluster->urgency_score * 10, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold {{ $colors['text'] }}">{{ number_format($productCluster->urgency_score, 2) }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
