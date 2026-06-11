<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('produk.index') }}" class="text-gray-400 hover:text-white transition-colors">
                        Produk
                    </a>
                </li>
                <li>
                    <span class="text-gray-600 mx-2">/</span>
                </li>
                <li>
                    <a href="{{ route('produk.index', ['category' => $product->category_id]) }}" class="text-gray-400 hover:text-white transition-colors">
                        {{ $product->category->name }}
                    </a>
                </li>
                <li>
                    <span class="text-gray-600 mx-2">/</span>
                </li>
                <li class="text-white font-medium">
                    {{ $product->name }}
                </li>
            </ol>
        </nav>

        <!-- Product Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Product Image -->
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl overflow-hidden border border-white/10 p-8">
                <img src="{{ $product->image_url }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-auto rounded-xl shadow-2xl">
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <!-- Category & Stock Badge -->
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-red-500/10 to-orange-500/10 border border-red-500/20 text-red-400">
                        {{ $product->category->name }}
                    </span>
                    <x-stock-badge :product="$product" />
                </div>

                <!-- Product Name -->
                <h1 class="text-4xl font-black text-white leading-tight">
                    {{ $product->name }}
                </h1>

                <!-- Cluster Priority -->
                @if($product->cluster && $product->cluster->priority_level === 'high')
                    <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-red-600/20 to-orange-500/20 border border-red-500/30 rounded-xl">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"></path>
                        </svg>
                        <span class="text-red-400 font-semibold">Produk Prioritas Tinggi</span>
                    </div>
                @endif

                <!-- Prices -->
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 space-y-4">
                    <div>
                        <p class="text-gray-400 text-sm mb-2">Harga Eceran</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-red-500">
                                {{ $product->formatted_price }}
                            </span>
                            <span class="text-gray-500">/ {{ $product->unit }}</span>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <p class="text-gray-400 text-sm mb-2">Harga Grosir</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-orange-500">
                                {{ $product->formatted_wholesale_price }}
                            </span>
                            <span class="text-gray-500">/ {{ $product->unit }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stock Info -->
                @if($product->stock)
                    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Stok Tersedia</span>
                            <span class="text-2xl font-bold text-white">{{ $product->stock->quantity }} {{ $product->unit }}</span>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                @if($product->description)
                    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6">
                        <h3 class="text-lg font-bold text-white mb-3">Deskripsi Produk</h3>
                        <p class="text-gray-300 leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    @php
                        $stock = $product->stock ? $product->stock->quantity : 0;
                        $isOutOfStock = $stock <= 0;
                    @endphp

                    @if($isOutOfStock)
                        @auth
                            <button type="button" onclick="notifyStock({{ $product->id }})" 
                                    class="flex-1 relative group/btn overflow-hidden px-6 py-4 rounded-xl text-base font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-blue-500/50">
                                <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-500"></span>
                                <span class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    Beritahu Saya
                                </span>
                            </button>
                        @else
                            <button type="button" disabled class="flex-1 px-6 py-4 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-500 text-base font-semibold cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endauth
                    @else
                        @auth
                            <button type="button" onclick="addToCart({{ $product->id }})" 
                                    class="flex-1 relative group/btn overflow-hidden px-6 py-4 rounded-xl text-base font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50">
                                <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                                <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                                    </svg>
                                    Tambah ke Keranjang
                                </span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                               class="flex-1 relative group/btn overflow-hidden px-6 py-4 rounded-xl text-base font-bold text-white text-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50 flex items-center justify-center">
                                <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                                <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative">Login untuk Membeli</span>
                            </a>
                        @endauth
                    @endif

                    <a href="{{ route('produk.index') }}" 
                       class="px-6 py-4 backdrop-blur-xl bg-white/5 border border-white/10 text-gray-300 rounded-xl text-base font-semibold hover:bg-white/10 hover:border-white/20 hover:text-white transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-black text-white">Produk Terkait</h2>
                    <a href="{{ route('produk.index', ['category' => $product->category_id]) }}" 
                       class="text-red-400 hover:text-red-300 font-semibold flex items-center gap-2 transition-colors">
                        Lihat Semua
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
