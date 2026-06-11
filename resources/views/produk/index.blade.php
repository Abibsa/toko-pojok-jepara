<x-app-layout>
    <x-slot name="title">Katalog Produk</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="mb-12 text-center animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-rose-400 to-orange-400">
                    Katalog Produk
                </span>
            </h1>
            <p class="text-gray-400 text-lg">Temukan produk berkualitas dengan harga grosir terbaik</p>
        </div>

        <!-- Filters -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-8 animate-slide-up">
            <form method="GET" action="{{ route('produk.index') }}" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Cari Produk</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Nama produk..." 
                                   class="w-full px-4 py-3 pl-11 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">
                            <svg class="absolute left-3.5 top-3.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                        <select name="category" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request()->query('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Status Stok</label>
                        <select name="stock_status" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="limited" {{ request('stock_status') == 'limited' ? 'selected' : '' }}>Stok Terbatas</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Prioritas</label>
                        <select name="priority" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">
                            <option value="">Semua Prioritas</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Prioritas Tinggi</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Prioritas Sedang</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Prioritas Rendah</option>
                        </select>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="flex flex-wrap items-end justify-between gap-4 pt-4 border-t border-white/10">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Urutkan</label>
                            <select name="sort" class="px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Harga</option>
                                <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stok</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Urutan</label>
                            <select name="order" class="px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>A-Z / Rendah-Tinggi</option>
                                <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Z-A / Tinggi-Rendah</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="group relative overflow-hidden px-6 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50">
                            <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                            <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="relative flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter
                            </span>
                        </button>
                        <a href="{{ route('produk.index') }}" class="px-6 py-3 backdrop-blur-xl bg-white/5 border border-white/10 text-gray-300 rounded-xl text-sm font-semibold hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div class="text-gray-400">
                Menampilkan <span class="text-white font-semibold">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> dari <span class="text-white font-semibold">{{ $products->total() }}</span> produk
            </div>
            
            @if(request()->hasAny(['search', 'category', 'stock_status', 'priority']))
                <div class="flex flex-wrap gap-2 text-sm">
                    @if(request('search'))
                        <span class="backdrop-blur-xl bg-blue-500/20 border border-blue-500/30 text-blue-300 px-3 py-1 rounded-full font-medium">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="backdrop-blur-xl bg-green-500/20 border border-green-500/30 text-green-300 px-3 py-1 rounded-full font-medium">
                            Kategori
                        </span>
                    @endif
                    @if(request('stock_status'))
                        <span class="backdrop-blur-xl bg-yellow-500/20 border border-yellow-500/30 text-yellow-300 px-3 py-1 rounded-full font-medium">
                            Status Stok
                        </span>
                    @endif
                    @if(request('priority'))
                        <span class="backdrop-blur-xl bg-red-500/20 border border-red-500/30 text-red-300 px-3 py-1 rounded-full font-medium">
                            Prioritas
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-16 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-r from-red-600/20 to-orange-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Tidak ada produk ditemukan</h3>
                <p class="text-gray-400 mb-8">Coba ubah filter pencarian Anda atau lihat semua produk</p>
                <a href="{{ route('produk.index') }}" class="group relative inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50">
                    <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 rounded-xl"></span>
                    <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    <span class="relative">Lihat Semua Produk</span>
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Real-time stock updates
        function updateStockStatuses() {
            const productCards = document.querySelectorAll('[data-product-id]');
            productCards.forEach(card => {
                const productId = card.dataset.productId;
                // Implementation for real-time updates
            });
        }

        // Update every 30 seconds
        setInterval(updateStockStatuses, 30000);
    </script>
    @endpush
</x-app-layout>
