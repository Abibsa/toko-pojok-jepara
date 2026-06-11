@extends('layouts.admin')

@section('title', 'Laporan')
@section('subtitle', 'Dashboard laporan penjualan dan stok')

@section('content')

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Sales Report Card -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 hover:scale-105 transition-all duration-300 animate-fade-in group">
            <div class="flex items-center justify-between mb-6">
                <div class="p-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-400">Laporan</p>
                    <h3 class="text-2xl font-black text-white">Penjualan</h3>
                </div>
            </div>

            <p class="text-gray-300 mb-6">
                Lihat laporan penjualan berdasarkan periode tertentu, analisis revenue, dan performa produk terlaris.
            </p>

            <div class="space-y-3 mb-6">
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Total revenue per periode</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Jumlah pesanan</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Produk terlaris</span>
                </div>
            </div>

            <a href="{{ route('admin.laporan.sales') }}" 
               class="block w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-bold text-center hover:from-cyan-500 hover:to-blue-600 transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50">
                Lihat Laporan Penjualan
            </a>
        </div>

        <!-- Stock Report Card -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 hover:scale-105 transition-all duration-300 animate-fade-in group" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between mb-6">
                <div class="p-4 rounded-xl bg-gradient-to-r from-green-600 to-emerald-500 shadow-lg shadow-green-500/30 group-hover:shadow-green-500/50 transition-all duration-300">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-400">Laporan</p>
                    <h3 class="text-2xl font-black text-white">Stok</h3>
                </div>
            </div>

            <p class="text-gray-300 mb-6">
                Monitor status stok produk, identifikasi stok kritis, dan produk yang habis untuk restock.
            </p>

            <div class="space-y-3 mb-6">
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Status stok semua produk</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Produk stok kritis</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Produk habis</span>
                </div>
            </div>

            <a href="{{ route('admin.laporan.stock') }}" 
               class="block w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold text-center hover:from-emerald-500 hover:to-green-600 transition-all duration-300 shadow-lg shadow-green-500/30 hover:shadow-green-500/50">
                Lihat Laporan Stok
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 animate-fade-in" style="animation-delay: 0.2s;">
        <h3 class="text-xl font-bold text-white mb-6">Statistik Cepat</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="text-center p-6 bg-white/5 rounded-xl border border-white/10">
                <div class="inline-flex p-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-500 shadow-lg shadow-purple-500/30 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-black text-white mb-2">{{ \App\Models\Order::count() }}</p>
                <p class="text-sm text-gray-400">Total Pesanan</p>
            </div>

            <!-- Total Products -->
            <div class="text-center p-6 bg-white/5 rounded-xl border border-white/10">
                <div class="inline-flex p-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg shadow-blue-500/30 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <p class="text-3xl font-black text-white mb-2">{{ \App\Models\Product::count() }}</p>
                <p class="text-sm text-gray-400">Total Produk</p>
            </div>

            <!-- Critical Stock -->
            <div class="text-center p-6 bg-white/5 rounded-xl border border-white/10">
                <div class="inline-flex p-3 rounded-xl bg-gradient-to-r from-yellow-600 to-orange-500 shadow-lg shadow-yellow-500/30 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-black text-yellow-400 mb-2">
                    {{ \App\Models\Product::whereHas('stock', function($q) { $q->where('quantity', '>', 0)->where('quantity', '<=', 10); })->count() }}
                </p>
                <p class="text-sm text-gray-400">Stok Kritis</p>
            </div>

            <!-- Out of Stock -->
            <div class="text-center p-6 bg-white/5 rounded-xl border border-white/10">
                <div class="inline-flex p-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-500 shadow-lg shadow-red-500/30 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <p class="text-3xl font-black text-red-400 mb-2">
                    {{ \App\Models\Product::whereHas('stock', function($q) { $q->where('quantity', '<=', 0); })->count() }}
                </p>
                <p class="text-sm text-gray-400">Stok Habis</p>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 mt-8 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-white">Export Laporan</h3>
                <p class="text-sm text-gray-400 mt-1">Download laporan dalam format CSV</p>
            </div>
            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.stok.export') }}" 
               class="flex items-center justify-between px-6 py-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-300 group-hover:text-white transition-colors">Export Stok</span>
                </div>
                <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>

            <a href="{{ route('admin.clustering.export') }}" 
               class="flex items-center justify-between px-6 py-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-300 group-hover:text-white transition-colors">Export Clustering</span>
                </div>
                <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>

            <button onclick="alert('Fitur export penjualan akan segera tersedia')" 
                    class="flex items-center justify-between px-6 py-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-300 group-hover:text-white transition-colors">Export Penjualan</span>
                </div>
                <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

@endsection
