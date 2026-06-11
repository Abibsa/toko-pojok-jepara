@extends('layouts.admin')

@section('title', 'Manajemen Produk')
@section('subtitle', 'Kelola katalog produk Toko Pojok Jepara')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Daftar Produk</h2>
        <a href="{{ route('admin.produk.create') }}" class="px-4 py-2 bg-gradient-to-r from-red-600 to-orange-500 rounded-xl text-white font-bold hover:from-orange-500 hover:to-red-600 transition-all shadow-lg shadow-red-500/30">
            + Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <form action="{{ route('admin.produk.index') }}" method="GET" class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..."
                           class="w-full pl-10 pr-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/30 transition-all">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/30 transition-all appearance-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Stock Status Filter -->
            <div>
                <select name="stock_status" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/30 transition-all appearance-none">
                    <option value="">Semua Stok</option>
                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia (>10)</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Terbatas (1-10)</option>
                    <option value="empty" {{ request('stock_status') == 'empty' ? 'selected' : '' }}>Habis (0)</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3 mt-4">
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-orange-500 rounded-xl text-white font-bold hover:from-orange-500 hover:to-red-600 transition-all text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Cari
            </button>
            @if(request()->hasAny(['search', 'category', 'stock_status']))
                <a href="{{ route('admin.produk.index') }}" class="px-5 py-2.5 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 font-semibold hover:bg-white/10 hover:text-white transition-all text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reset
                </a>
            @endif
        </div>
    </form>

    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($products as $product)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="{{ $product->image_url }}" class="w-12 h-12 object-cover rounded-xl border border-white/10">
                                <div>
                                    <div class="text-white font-semibold">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-400">SKU: {{ $product->id }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ $product->category->name }}</td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $product->formatted_price }}</div>
                                <div class="text-xs text-gray-400">Grosir: {{ $product->formatted_wholesale_price }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold {{ ($product->stock->quantity ?? 0) > 10 ? 'text-green-400' : 'text-yellow-400' }}">{{ $product->stock->quantity ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <a href="{{ route('admin.produk.show', $product->slug) }}" class="p-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30">View</a>
                                <a href="{{ route('admin.produk.edit', $product->slug) }}" class="p-2 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30">Edit</a>
                                <form action="{{ route('admin.produk.destroy', $product->slug) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada produk ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-white/10">
            {{ $products->links() }}
        </div>
    </div>
@endsection
