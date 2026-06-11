<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Toko Pojok Jepara - Toko grosir terpercaya dengan sistem stok real-time dan harga terbaik untuk kebutuhan bisnis Anda.">

    <title>{{ $title ?? 'Toko Pojok Jepara' }} - E-Commerce Grosir</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#DC2626',
                        secondary: '#1F2937',
                        accent: '#F9FAFB'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 5px rgba(220, 38, 38, 0.5)' },
                            '100%': { boxShadow: '0 0 20px rgba(220, 38, 38, 0.8)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100 min-h-screen">
    <div class="min-h-screen">
        <!-- Navigation - Glassmorphism -->
        <nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl bg-gray-900/60 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('home') }}" class="flex items-center group gap-3">
                                <img src="{{ asset('images/logo.png') }}" alt="Toko Pojok Jepara" class="h-10 w-auto object-contain rounded-lg shadow-[0_0_15px_rgba(220,38,38,0.5)] group-hover:shadow-[0_0_25px_rgba(220,38,38,0.8)] transition-all duration-300">
                                <div>
                                    <div class="text-xl font-black text-red-500 tracking-wide">TOKO POJOK</div>
                                    <div class="text-xs text-gray-400 tracking-widest leading-none">JEPARA</div>
                                </div>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:ml-12 md:flex md:space-x-1">
                            <a href="{{ route('home') }}" class="relative px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300 group">
                                <span class="relative z-10">Beranda</span>
                                <span class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></span>
                                @if(request()->routeIs('home'))
                                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-600 to-orange-500"></span>
                                @endif
                            </a>
                            <a href="{{ route('produk.index') }}" class="relative px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300 group">
                                <span class="relative z-10">Produk</span>
                                <span class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></span>
                                @if(request()->routeIs('produk.*'))
                                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-600 to-orange-500"></span>
                                @endif
                            </a>
                            @auth
                                <a href="{{ route('pesanan.index') }}" class="relative px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300 group">
                                    <span class="relative z-10">Pesanan</span>
                                    <span class="absolute inset-0 bg-white/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300"></span>
                                    @if(request()->routeIs('pesanan.*'))
                                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-600 to-orange-500"></span>
                                    @endif
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-3">
                        <!-- Search Bar -->
                        <div class="hidden md:block">
                            <form action="{{ route('produk.index') }}" method="GET" class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari produk..." 
                                       class="w-64 px-4 py-2 pl-10 bg-gray-800/50 border border-white/10 rounded-full text-sm text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </form>
                        </div>

                        @auth
                            <!-- Cart -->
                            <a href="{{ route('keranjang.index') }}" class="relative p-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-full transition-all duration-300 group" aria-label="Keranjang Belanja">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                                <span id="cart-count" class="absolute -top-1 -right-1 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold shadow-lg shadow-red-500/50">0</span>
                            </a>

                            <!-- Notifications -->
                            <!-- Notifications -->
                            <a href="{{ route('notifications.index') }}" class="relative p-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-full transition-all duration-300" aria-label="Notifikasi">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span id="notification-count" class="absolute -top-1 -right-1 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold shadow-lg shadow-red-500/50 hidden">0</span>
                            </a>

                            <!-- User Menu -->
                            <div class="relative">
                                <button id="user-menu-btn" class="flex items-center space-x-2 px-3 py-2 rounded-full bg-white/5 hover:bg-white/10 transition-all duration-300 border border-white/10">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-red-600 to-orange-500 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-300 font-medium hidden lg:block">{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div id="user-menu" class="hidden absolute right-0 mt-2 w-56 backdrop-blur-xl bg-gray-900/90 border border-white/10 rounded-2xl shadow-2xl py-2 z-50 animate-fade-in">
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-all duration-200">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Dashboard Admin
                                        </a>
                                    @endif
                                    <a href="{{ route('pesanan.index') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Pesanan Saya
                                    </a>
                                    <div class="border-t border-white/10 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-200">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white transition-all duration-300">Login</a>
                            <a href="{{ route('register') }}" class="relative px-6 py-2.5 text-sm font-semibold text-white rounded-full overflow-hidden group">
                                <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                                <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative">Daftar</span>
                            </a>
                        @endauth

                        <!-- Mobile menu button -->
                        <button id="mobile-menu-btn" class="md:hidden p-2.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-full transition-all duration-300" aria-label="Buka Menu Navigasi">
                            <svg id="mobile-menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg id="mobile-menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-white/10">
                <div class="px-4 py-4 space-y-2">
                    <form action="{{ route('produk.index') }}" method="GET" class="relative mb-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="w-full px-4 py-3 pl-10 bg-gray-800/50 border border-white/10 rounded-xl text-sm text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50">
                        <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </form>
                    <a href="{{ route('home') }}" class="block px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('home') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }} transition-all">Beranda</a>
                    <a href="{{ route('produk.index') }}" class="block px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('produk.*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }} transition-all">Produk</a>
                    @auth
                        <a href="{{ route('pesanan.index') }}" class="block px-4 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('pesanan.*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/5' }} transition-all">Pesanan</a>
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Spacer for fixed navbar -->
        <div class="h-20"></div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="flash-message fixed top-24 right-4 z-50 animate-slide-up max-w-md">
                <div class="backdrop-blur-xl bg-green-500/10 border border-green-500/30 text-green-400 px-6 py-4 rounded-2xl shadow-2xl shadow-green-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-400 hover:text-green-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message fixed top-24 right-4 z-50 animate-slide-up max-w-md">
                <div class="backdrop-blur-xl bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl shadow-2xl shadow-red-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="flex-1 text-sm font-medium">{{ session('error') }}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="flash-message fixed top-24 right-4 z-50 animate-slide-up max-w-md">
                <div class="backdrop-blur-xl bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-6 py-4 rounded-2xl shadow-2xl shadow-yellow-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="flex-1 text-sm font-medium">{{ session('warning') }}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="flash-message fixed top-24 right-4 z-50 animate-slide-up max-w-md">
                <div class="backdrop-blur-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 px-6 py-4 rounded-2xl shadow-2xl shadow-blue-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="flex-1 text-sm font-medium">{{ session('info') }}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-message fixed top-24 right-4 z-50 animate-slide-up max-w-md">
                <div class="backdrop-blur-xl bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl shadow-2xl shadow-red-500/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold mb-2">Terjadi kesalahan validasi:</p>
                            <ul class="text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="relative">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="relative mt-24 border-t border-white/10">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-950/50"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logo.png') }}" alt="Toko Pojok Jepara" class="h-12 w-auto object-contain rounded-xl shadow-[0_0_15px_rgba(220,38,38,0.5)]">
                            <div>
                                <div class="text-xl font-black text-red-500 tracking-wide">TOKO POJOK</div>
                                <div class="text-xs text-gray-500 tracking-widest leading-none">JEPARA</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">Toko grosir terpercaya dengan sistem stok real-time dan harga terbaik untuk kebutuhan bisnis Anda.</p>
                    </div>
                    
                    <div>
                        <h4 class="text-white font-semibold mb-4 tracking-wide">Kategori</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('produk.index', ['search' => 'sembako']) }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Sembako
                            </a></li>
                            <li><a href="{{ route('produk.index', ['search' => 'minuman']) }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Minuman
                            </a></li>
                            <li><a href="{{ route('produk.index', ['search' => 'snack']) }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Snack
                            </a></li>
                            <li><a href="{{ route('produk.index', ['search' => 'kebersihan']) }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Kebersihan
                            </a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-white font-semibold mb-4 tracking-wide">Layanan</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('produk.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Stok Real-Time
                            </a></li>
                            <li><a href="{{ route('produk.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Harga Grosir
                            </a></li>
                            <li><a href="{{ route('produk.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Pengiriman Cepat
                            </a></li>
                            <li><a href="{{ route('produk.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors duration-200 flex items-center group">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Customer Support
                            </a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-white font-semibold mb-4 tracking-wide">Kontak</h4>
                        <ul class="space-y-3 text-sm text-gray-400">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 mr-2 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Jl. Raya Pojok No. 123, Jepara
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                (0291) 123-4567
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                info@tokopojokjepara.com
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Senin - Sabtu: 08:00 - 17:00
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-white/10 mt-12 pt-8 text-center">
                    <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} Toko Pojok Jepara. Semua hak dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('mobile-menu-icon-open');
            const iconClose = document.getElementById('mobile-menu-icon-close');
            menu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });

        // User menu toggle
        document.getElementById('user-menu-btn')?.addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userMenu = document.getElementById('user-menu');
            if (userMenuBtn && userMenu && !userMenuBtn.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Update cart count
        function updateCartCount() {
            @auth
            fetch('{{ route("api.cart.total") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.total;
                })
                .catch(error => console.log('Cart count update failed'));
            @endauth
        }

        // Update notification count
        function updateNotificationCount() {
            @auth
            fetch('{{ route("notifications.count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-count');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => console.log('Notification count update failed'));
            @endauth
        }

        // Global function: Add to Cart (uses modal instead of prompt)
        function addToCart(productId) {
            openQtyModal(productId);
        }

        let pendingProductId = null;
        function openQtyModal(productId) {
            pendingProductId = productId;
            document.getElementById('qty-modal-input').value = 1;
            document.getElementById('qty-modal').classList.remove('hidden');
        }
        function closeQtyModal() {
            document.getElementById('qty-modal').classList.add('hidden');
            pendingProductId = null;
        }
        function changeModalQty(delta) {
            const input = document.getElementById('qty-modal-input');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            input.value = val;
        }
        function confirmAddToCart() {
            const qty = parseInt(document.getElementById('qty-modal-input').value);
            if (pendingProductId && qty > 0) {
                doAddToCart(pendingProductId, qty);
            }
            closeQtyModal();
        }
        function doAddToCart(productId, quantity) {
            fetch('{{ route("keranjang.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
            });
        }

        // Global function: Notify Stock
        function notifyStock(productId) {
            fetch('{{ route("stock-alert.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Gagal berlangganan notifikasi', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat berlangganan notifikasi', 'error');
            });
        }

        // Initialize counts
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateNotificationCount();
            
            // Update every 30 seconds
            setInterval(updateCartCount, 30000);
            setInterval(updateNotificationCount, 30000);

            // Auto-hide flash messages after 5 seconds (only target .flash-message, not JS notifications)
            setTimeout(() => {
                document.querySelectorAll('.flash-message').forEach(el => {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                });
            }, 5000);
        });

        // Unified showNotification function (single version, removes duplicate)
        function showNotification(message, type = 'success') {
            document.querySelectorAll('.js-notification').forEach(n => n.remove());
            const colors = {
                success: 'bg-green-500/10 border-green-500/30 text-green-400 shadow-green-500/20',
                error: 'bg-red-500/10 border-red-500/30 text-red-400 shadow-red-500/20',
                warning: 'bg-yellow-500/10 border-yellow-500/30 text-yellow-400 shadow-yellow-500/20',
                info: 'bg-blue-500/10 border-blue-500/30 text-blue-400 shadow-blue-500/20'
            };
            const icons = {
                success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
            };
            const notification = document.createElement('div');
            notification.className = 'js-notification fixed top-24 right-4 z-50 animate-slide-up max-w-md transition-all duration-300';
            notification.innerHTML = `
                <div class="backdrop-blur-xl ${colors[type] || colors.success} border px-6 py-4 rounded-2xl shadow-2xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type] || icons.success}</svg>
                        <p class="flex-1 text-sm font-medium">${message}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="hover:opacity-70 transition-opacity" aria-label="Tutup notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-20px)';
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }

        // Global error handler for AJAX requests
        window.handleAjaxError = function(error) {
            console.error('AJAX Error:', error);
            if (error.response) {
                const status = error.response.status;
                if (status === 401) {
                    showNotification('Sesi Anda telah berakhir. Silakan login kembali.', 'warning');
                    setTimeout(() => window.location.href = '{{ route("login") }}', 2000);
                } else if (status === 403) {
                    showNotification('Anda tidak memiliki akses.', 'error');
                } else if (status >= 500) {
                    showNotification('Terjadi kesalahan server.', 'error');
                } else {
                    showNotification('Terjadi kesalahan.', 'error');
                }
            } else {
                showNotification('Tidak dapat terhubung ke server.', 'error');
            }
        };
    </script>

    <!-- Quantity Modal -->
    <div id="qty-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQtyModal()"></div>
        <div class="relative backdrop-blur-xl bg-gray-900/90 border border-white/10 rounded-2xl p-6 w-80 shadow-2xl animate-fade-in">
            <h3 class="text-lg font-bold text-white mb-4">Jumlah Pembelian</h3>
            <div class="flex items-center justify-center gap-4 mb-6">
                <button onclick="changeModalQty(-1)" class="w-10 h-10 rounded-full bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-all flex items-center justify-center text-xl font-bold">−</button>
                <input type="number" id="qty-modal-input" value="1" min="1" class="w-20 text-center bg-gray-800/50 border border-white/10 rounded-xl py-2 text-white text-lg font-bold focus:outline-none focus:ring-2 focus:ring-red-500/50">
                <button onclick="changeModalQty(1)" class="w-10 h-10 rounded-full bg-white/10 border border-white/20 text-white hover:bg-white/20 transition-all flex items-center justify-center text-xl font-bold">+</button>
            </div>
            <div class="flex gap-3">
                <button onclick="closeQtyModal()" class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 text-gray-300 rounded-xl font-semibold hover:bg-white/10 transition-all">Batal</button>
                <button onclick="confirmAddToCart()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl font-bold hover:scale-105 transition-all shadow-lg shadow-red-500/30">Tambah</button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>