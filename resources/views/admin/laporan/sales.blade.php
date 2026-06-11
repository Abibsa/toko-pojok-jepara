@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('subtitle', 'Analisis penjualan dan revenue berdasarkan periode')

@section('content')

    {{-- Filter Periode --}}
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-8 animate-fade-in">
        <form method="GET" action="{{ route('admin.laporan.sales') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date"
                       value="{{ is_string($startDate) ? $startDate : (is_object($startDate) ? $startDate->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d')) }}"
                       class="px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date"
                       value="{{ is_string($endDate) ? $endDate : (is_object($endDate) ? $endDate->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d')) }}"
                       class="px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300">
            </div>
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-bold hover:from-cyan-500 hover:to-blue-600 transition-all duration-300 shadow-lg shadow-blue-500/30">
                Tampilkan
            </button>
            <a href="{{ route('admin.laporan.export', 'sales') }}"
               class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold hover:from-emerald-500 hover:to-green-600 transition-all duration-300 shadow-lg shadow-green-500/30">
                Export CSV
            </a>
            <a href="{{ route('admin.laporan.index') }}"
               class="px-6 py-3 backdrop-blur-xl bg-white/5 border border-white/10 text-gray-300 rounded-xl font-semibold hover:bg-white/10 transition-all duration-300">
                ← Kembali
            </a>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in">
            <p class="text-sm font-semibold text-gray-400 mb-2">Total Pesanan</p>
            <p class="text-4xl font-black text-white">{{ $totalOrders }}</p>
        </div>
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in" style="animation-delay:.05s">
            <p class="text-sm font-semibold text-gray-400 mb-2">Total Revenue</p>
            <p class="text-4xl font-black text-green-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in" style="animation-delay:.1s">
            <p class="text-sm font-semibold text-gray-400 mb-2">Rata-rata / Pesanan</p>
            <p class="text-4xl font-black text-blue-400">
                Rp {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 0, ',', '.') : '0' }}
            </p>
        </div>
    </div>

    {{-- Tabel Pesanan --}}
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in" style="animation-delay:.15s">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-lg font-bold text-white">Daftar Pesanan</h3>
            <p class="text-sm text-gray-400 mt-1">{{ $totalOrders }} pesanan (tidak termasuk yang dibatalkan)</p>
        </div>

        @if($orders->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-400 text-lg">Tidak ada pesanan pada periode ini</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Kode Pesanan</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Pelanggan</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Status</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Item</th>
                            <th class="text-right py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Total</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-400 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($orders as $order)
                            <tr class="hover:bg-white/5 transition-colors duration-150">
                                <td class="py-4 px-6">
                                    <span class="font-mono text-sm font-semibold text-blue-400">{{ $order->order_code }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-300">
                                    {{ $order->user->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        {{ $order->status_badge }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-400">
                                    {{ $order->orderItems->sum('quantity') }} item
                                </td>
                                <td class="py-4 px-6 text-right font-semibold text-white">
                                    {{ $order->formatted_total }}
                                </td>
                                <td class="py-4 px-6 text-gray-400 text-sm">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-white/20">
                        <tr>
                            <td colspan="4" class="py-4 px-6 font-bold text-gray-300">Total Revenue</td>
                            <td class="py-4 px-6 text-right font-black text-green-400 text-lg">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

@endsection
