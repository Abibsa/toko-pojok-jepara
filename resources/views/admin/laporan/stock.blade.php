@extends('layouts.admin')

@section('title', 'Laporan Stok')
@section('subtitle', 'Analisis ketersediaan stok produk')

@section('content')

    {{-- Filter & Actions --}}
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-8 animate-fade-in flex flex-wrap gap-4 justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-white">Status Inventaris</h3>
            <p class="text-sm text-gray-400 mt-1">Pantau stok yang butuh perhatian</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.laporan.export', 'stock') }}"
               class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl font-bold hover:from-emerald-500 hover:to-green-600 transition-all duration-300 shadow-lg shadow-green-500/30">
                Export CSV
            </a>
            <a href="{{ route('admin.laporan.index') }}"
               class="px-6 py-3 backdrop-blur-xl bg-white/5 border border-white/10 text-gray-300 rounded-xl font-semibold hover:bg-white/10 transition-all duration-300">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in">
            <p class="text-sm font-semibold text-gray-400 mb-2">Total Produk</p>
            <p class="text-4xl font-black text-white">{{ $stocks->count() }}</p>
        </div>
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in" style="animation-delay:.05s">
            <p class="text-sm font-semibold text-gray-400 mb-2">Stok Kritis (<= 10)</p>
            <p class="text-4xl font-black text-yellow-400">{{ $criticalStock->count() }}</p>
        </div>
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 text-center animate-fade-in" style="animation-delay:.1s">
            <p class="text-sm font-semibold text-gray-400 mb-2">Stok Habis</p>
            <p class="text-4xl font-black text-red-400">{{ $outOfStock->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Tabel Stok Kritis --}}
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in" style="animation-delay:.15s">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        Stok Kritis
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">Sisa 1-10 item</p>
                </div>
            </div>

            @if($criticalStock->isEmpty())
                <div class="p-10 text-center">
                    <p class="text-gray-400">Tidak ada produk dengan stok kritis.</p>
                </div>
            @else
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-900">
                            <tr class="border-b border-white/10">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Produk</th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Kategori</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($criticalStock as $item)
                                <tr class="hover:bg-white/5 transition-colors duration-150">
                                    <td class="py-3 px-4 text-gray-300 font-medium">{{ $item->product->name }}</td>
                                    <td class="py-3 px-4 text-center text-gray-400">{{ $item->product->category->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-yellow-400">{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Tabel Stok Habis --}}
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in" style="animation-delay:.2s">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        Stok Habis
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">Harus segera di-restock</p>
                </div>
            </div>

            @if($outOfStock->isEmpty())
                <div class="p-10 text-center">
                    <p class="text-gray-400">Tidak ada produk yang stoknya habis.</p>
                </div>
            @else
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-900">
                            <tr class="border-b border-white/10">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Produk</th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Kategori</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($outOfStock as $item)
                                <tr class="hover:bg-white/5 transition-colors duration-150">
                                    <td class="py-3 px-4 text-gray-300 font-medium">{{ $item->product->name }}</td>
                                    <td class="py-3 px-4 text-center text-gray-400">{{ $item->product->category->name ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.stok.index', ['search' => $item->product->name]) }}" class="text-blue-400 hover:text-blue-300 underline text-xs">
                                            Restock
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
