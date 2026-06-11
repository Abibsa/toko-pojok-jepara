@props(['product'])

@php
    $stock = $product->stock ? $product->stock->quantity : 0;
    $isOutOfStock = $stock <= 0;
    $cluster = $product->cluster;
@endphp

<div class="group relative backdrop-blur-xl bg-gray-900/70 rounded-2xl overflow-hidden border border-white/10 hover:border-red-500/50 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-500/30 animate-fade-in">
    <!-- Product Image -->
    <div class="relative overflow-hidden">
        <img src="{{ $product->image_url }}" 
             alt="{{ $product->name }}" 
             loading="lazy"
             class="w-full h-56 object-cover transition-transform duration-700 group-hover:scale-110">
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-60 pointer-events-none"></div>
        
        <!-- Cluster Priority Badge -->
        @if($cluster && $cluster->priority_level === 'high')
            <div class="absolute top-3 left-3 z-10 pointer-events-none">
                <div class="relative">
                    <div class="absolute inset-0 bg-red-600 rounded-full blur-md animate-pulse"></div>
                    <span class="relative flex items-center gap-1.5 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-lg shadow-red-500/50">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"></path>
                        </svg>
                        HOT
                    </span>
                </div>
            </div>
        @endif
        
        <!-- Stock Badge -->
        <div class="absolute top-3 right-3 z-10 pointer-events-none">
            <x-stock-badge :product="$product" />
        </div>
    </div>

    <!-- Product Info -->
    <div class="p-5 space-y-3 relative z-20">
        <!-- Category -->
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium tracking-wide uppercase">{{ $product->category->name }}</span>
            @if($cluster && $cluster->priority_level === 'high')
                <span class="text-xs text-red-400 font-semibold">• Stok: {{ $stock }}</span>
            @else
                <span class="text-xs text-gray-400 font-semibold">• Stok: {{ $stock }}</span>
            @endif
        </div>
        
        <!-- Product Name -->
        <h3 class="font-bold text-white text-lg leading-tight line-clamp-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-400 group-hover:to-orange-400 transition-all duration-300">
            <a href="{{ route('produk.show', $product->slug) }}" class="hover:underline">
                {{ $product->name }}
            </a>
        </h3>
        
        <!-- Prices -->
        <div class="space-y-2">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-red-500">
                    {{ $product->formatted_price }}
                </span>
                <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-500/20 rounded-lg">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-orange-300 font-semibold">Grosir: {{ $product->formatted_wholesale_price }}</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-2 pt-2 relative z-30">
            @if($isOutOfStock)
                @auth
                    <button type="button" onclick="notifyStock({{ $product->id }})" 
                            class="flex-1 relative group/btn overflow-hidden px-4 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-blue-500/50 cursor-pointer">
                        <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-500"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            Beritahu
                        </span>
                    </button>
                @else
                    <button type="button" disabled class="flex-1 relative px-4 py-3 rounded-xl bg-gray-800/50 border border-gray-700 text-gray-500 text-sm font-semibold cursor-not-allowed">
                        Stok Habis
                    </button>
                @endauth
            @else
                @auth
                    <button type="button" onclick="addToCart({{ $product->id }})" 
                            class="flex-1 relative group/btn overflow-hidden px-4 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50 cursor-pointer">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            Keranjang
                        </span>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                       class="flex-1 relative group/btn overflow-hidden px-4 py-3 rounded-xl text-sm font-bold text-white text-center transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-red-500/50 flex items-center justify-center">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative">Login</span>
                    </a>
                @endauth
            @endif
            
            <a href="{{ route('produk.show', $product->slug) }}" 
               class="flex items-center justify-center px-4 py-3 backdrop-blur-xl bg-white/5 border border-white/10 text-gray-300 rounded-xl text-sm font-semibold hover:bg-white/10 hover:border-white/20 hover:text-white transition-all duration-300 hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Glow Effect on Hover -->
    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none z-0">
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-red-600/20 via-rose-500/20 to-orange-400/20 blur-xl"></div>
    </div>
</div>
