@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan aktivitas toko dan monitoring stok real-time')

@section('content')

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Products -->
        <div class="group backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-5 hover:border-blue-500/50 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-blue-500/20 animate-fade-in">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-semibold text-gray-400 tracking-wide">Total Produk</p>
                    <p class="text-xl font-black text-white mt-1">{{ $stockStats['total_products'] }}</p>
                </div>
            </div>
        </div>

        <!-- Critical Stock -->
        <div class="group backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-5 hover:border-yellow-500/50 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-yellow-500/20 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-gradient-to-r from-yellow-600 to-orange-500 shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform duration-300 animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-semibold text-gray-400 tracking-wide">Stok Kritis</p>
                    <p class="text-xl font-black text-yellow-400 mt-1">{{ $stockStats['critical_stock'] }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="group backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-5 hover:border-green-500/50 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-green-500/20 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-500 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-semibold text-gray-400 tracking-wide">Pesanan Hari Ini</p>
                    <p class="text-xl font-black text-white mt-1">{{ $todayOrders }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="group backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-5 hover:border-red-500/50 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-red-500/20 animate-fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-500 shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs lg:text-sm font-semibold text-gray-400 tracking-wide">Revenue Hari Ini</p>
                    <p class="text-xl font-black text-white mt-1 whitespace-nowrap tracking-tight">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Weekly Sales Chart -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in">
            <h3 class="text-xl font-bold text-white mb-6">Penjualan 7 Hari Terakhir</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in">
            <h3 class="text-xl font-bold text-white mb-6">Produk Terlaris Bulan Ini</h3>
            <div class="space-y-3">
                @forelse($topProducts as $index => $item)
                    <div class="flex items-center justify-between p-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg shadow-red-500/30">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-400">{{ $item->product->category->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400">{{ $item->total_sold }}</p>
                            <p class="text-xs text-gray-500">unit terjual</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Belum ada data penjualan bulan ini</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Sales Chart with better styling
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($weeklyData, 'date')) !!},
                datasets: [{
                    label: 'Pesanan',
                    data: {!! json_encode(array_column($weeklyData, 'orders')) !!},
                    borderColor: '#DC2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#DC2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#9CA3AF'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9CA3AF'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false
                    }
                }
            }
        });
    </script>
@endpush
