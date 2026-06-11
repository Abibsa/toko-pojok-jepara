<x-app-layout>
    <x-slot name="title">Selamat Datang di Toko Pojok Jepara</x-slot>

    <!-- Hero Section - Futuristic -->
    <div class="relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 via-rose-500/10 to-orange-400/20"></div>
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-red-600/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center space-y-8 animate-fade-in">
                <!-- Logo Badge -->
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-orange-500 rounded-2xl blur-2xl opacity-75 animate-pulse"></div>
                        <div class="relative backdrop-blur-xl bg-gray-900/70 border border-white/20 rounded-2xl px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 bg-gradient-to-r from-white/10 to-white/5 rounded-2xl flex items-center justify-center p-2 border border-white/10 shadow-[0_0_20px_rgba(220,38,38,0.3)]">
                                    <img src="{{ asset('images/logo.png') }}" alt="Toko Pojok Jepara" class="w-full h-full object-contain">
                                </div>
                                <div class="text-left">
                                    <div class="text-2xl font-black text-red-500">TOKO POJOK</div>
                                    <div class="text-xs text-gray-400 tracking-widest">JEPARA</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black text-white leading-tight">
                    <span class="block">Grosir Modern</span>
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-rose-400 to-orange-400">
                        Stok Real-Time
                    </span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Platform e-commerce dengan teknologi K-Means Clustering untuk monitoring stok otomatis dan harga grosir terbaik
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                    <a href="{{ route('produk.index') }}" 
                       class="group relative overflow-hidden px-8 py-4 rounded-full text-lg font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                            </svg>
                            Lihat Produk
                        </span>
                    </a>
                    @guest
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 backdrop-blur-xl bg-white/10 border-2 border-white/20 text-white rounded-full text-lg font-bold hover:bg-white/20 hover:border-white/30 transition-all duration-300 hover:scale-105">
                            Daftar Sekarang
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-4">Mengapa Pilih Kami?</h2>
                <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                    Teknologi terdepan untuk pengalaman belanja grosir yang optimal
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group relative backdrop-blur-xl bg-gray-900/70 rounded-2xl p-8 border border-white/10 hover:border-red-500/50 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-500/20 animate-fade-in">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-r from-red-600 to-orange-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Stok Real-Time</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Pantau ketersediaan produk secara langsung dengan sistem monitoring stok otomatis berbasis AI
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="group relative backdrop-blur-xl bg-gray-900/70 rounded-2xl p-8 border border-white/10 hover:border-orange-500/50 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-orange-500/20 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-600/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-600 to-red-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">K-Means Clustering</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Algoritma cerdas untuk prioritas produk berdasarkan pola pembelian dan popularitas
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="group relative backdrop-blur-xl bg-gray-900/70 rounded-2xl p-8 border border-white/10 hover:border-rose-500/50 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:shadow-2xl hover:shadow-rose-500/20 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-600/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative">
                        <div class="w-16 h-16 bg-gradient-to-r from-rose-600 to-orange-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Harga Grosir</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Dapatkan harga khusus grosir otomatis untuk pembelian dalam jumlah besar (≥5 unit)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    @if(isset($categories) && $categories->count() > 0)
        <div class="relative py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-white mb-4">Kategori Produk</h2>
                    <p class="text-gray-400 text-lg">Temukan berbagai kebutuhan bisnis Anda</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('kategori.show', $category->slug) }}" 
                           class="group relative backdrop-blur-xl bg-gray-900/70 rounded-2xl p-6 border border-white/10 hover:border-red-500/50 transition-all duration-300 hover:scale-110 hover:-translate-y-2 hover:shadow-xl hover:shadow-red-500/20 text-center">
                            <div class="text-4xl mb-3 group-hover:scale-125 transition-transform duration-300">
                                @switch($category->name)
                                    @case('Sembako') 🌾 @break
                                    @case('Minuman') 🥤 @break
                                    @case('Snack') 🍪 @break
                                    @case('Kebersihan') 🧽 @break
                                    @case('Bumbu Dapur') 🧂 @break
                                    @case('Perawatan Diri') 🧴 @break
                                    @default 📦
                                @endswitch
                            </div>
                            <h3 class="font-bold text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-red-400 group-hover:to-orange-400 transition-all duration-300">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ $category->products_count ?? 0 }} produk
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Featured Products -->
    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
        <div class="relative py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-white mb-4">Produk Unggulan</h2>
                    <p class="text-gray-400 text-lg">Produk terpopuler dengan stok tersedia</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                    @foreach($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="text-center">
                    <a href="{{ route('produk.index') }}" 
                       class="group relative inline-flex items-center gap-3 px-8 py-4 rounded-full text-lg font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 rounded-full"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative">Lihat Semua Produk</span>
                        <svg class="relative w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red-600/20 via-rose-500/20 to-orange-400/20"></div>
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-96 h-96 bg-red-600/30 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-500/30 rounded-full blur-3xl animate-float" style="animation-delay: 1.5s;"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Siap Memulai Bisnis Anda?</h2>
            <p class="text-xl text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                Bergabunglah dengan ribuan pebisnis yang sudah mempercayai Toko Pojok Jepara sebagai supplier utama mereka
            </p>
            
            @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" 
                       class="group relative overflow-hidden px-8 py-4 rounded-full text-lg font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative">Daftar Gratis Sekarang</span>
                    </a>
                    <a href="{{ route('produk.index') }}" 
                       class="px-8 py-4 backdrop-blur-xl bg-white/10 border-2 border-white/20 text-white rounded-full text-lg font-bold hover:bg-white/20 hover:border-white/30 transition-all duration-300 hover:scale-105">
                        Jelajahi Produk
                    </a>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('produk.index') }}" 
                       class="group relative overflow-hidden px-8 py-4 rounded-full text-lg font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50">
                        <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                        <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <span class="relative">Mulai Belanja</span>
                    </a>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-8 py-4 backdrop-blur-xl bg-white/10 border-2 border-white/20 text-white rounded-full text-lg font-bold hover:bg-white/20 hover:border-white/30 transition-all duration-300 hover:scale-105">
                            Dashboard Admin
                        </a>
                    @endif
                </div>
            @endguest
        </div>
    </div>
</x-app-layout>
