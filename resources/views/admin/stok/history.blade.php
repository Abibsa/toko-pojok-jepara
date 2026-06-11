@extends('layouts.admin')

@section('title', 'Riwayat Stok')
@section('subtitle', 'Histori perubahan stok produk')

@section('content')
    <!-- Filter Section -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-6">
        <form method="GET" action="{{ route('admin.stok.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Product Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Produk</label>
                <select name="product_id" class="w-full px-4 py-2.5 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
                    <option value="">Semua Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2.5 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-4 py-2.5 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-4 py-2.5 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl font-bold hover:scale-105 transition-all shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.stok.history') }}" class="px-6 py-2.5 bg-white/5 border border-white/10 text-gray-300 rounded-xl font-semibold hover:bg-white/10 transition-all">
                    Reset
                </a>
                <a href="{{ route('admin.stok.index') }}" class="px-6 py-2.5 bg-white/5 border border-white/10 text-gray-300 rounded-xl font-semibold hover:bg-white/10 transition-all">
                    Kembali ke Stok
                </a>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Perubahan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($histories as $history)
                        <tr class="hover:bg-white/5 transition-colors">
                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">{{ $history->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $history->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $history->product->image_url }}" 
                                         alt="{{ $history->product->name }}" 
                                         class="w-10 h-10 object-cover rounded-lg border border-white/10">
                                    <div>
                                        <div class="text-sm font-semibold text-white">{{ $history->product->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $history->product->category->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($history->type === 'in')
                                    <span class="px-3 py-1 bg-green-500/20 border border-green-500/30 text-green-400 rounded-full text-xs font-bold">
                                        Masuk
                                    </span>
                                @elseif($history->type === 'out')
                                    <span class="px-3 py-1 bg-red-500/20 border border-red-500/30 text-red-400 rounded-full text-xs font-bold">
                                        Keluar
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-500/20 border border-yellow-500/30 text-yellow-400 rounded-full text-xs font-bold">
                                        Penyesuaian
                                    </span>
                                @endif
                            </td>

                            <!-- Change -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold {{ $history->quantity_change > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $history->quantity_change > 0 ? '+' : '' }}{{ $history->quantity_change }}
                                </div>
                            </td>

                            <!-- Stock -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">
                                    <span class="text-gray-500">{{ $history->quantity_before }}</span>
                                    <svg class="w-4 h-4 inline-block mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    <span class="text-white font-bold">{{ $history->quantity_after }}</span>
                                </div>
                            </td>

                            <!-- User -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $history->user->name ?? 'System' }}</div>
                            </td>

                            <!-- Note -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-400 max-w-xs truncate" title="{{ $history->note }}">
                                    {{ $history->note ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold">Tidak ada riwayat stok</p>
                                    <p class="text-sm mt-2">Belum ada perubahan stok yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($histories->hasPages())
            <div class="p-6 border-t border-white/10">
                {{ $histories->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Stats -->
    @if($histories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-500/20 border border-green-500/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">{{ $histories->where('type', 'in')->count() }}</div>
                        <div class="text-sm text-gray-400">Stok Masuk</div>
                    </div>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/20 border border-red-500/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">{{ $histories->where('type', 'out')->count() }}</div>
                        <div class="text-sm text-gray-400">Stok Keluar</div>
                    </div>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/20 border border-yellow-500/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">{{ $histories->where('type', 'adjustment')->count() }}</div>
                        <div class="text-sm text-gray-400">Penyesuaian</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
