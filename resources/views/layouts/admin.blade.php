<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - Toko Pojok Jepara</title>

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
                        'slide-right': 'slideRight 0.3s ease-out'
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
                        slideRight: {
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100">
    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <div id="admin-sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 backdrop-blur-xl bg-gray-900/80 border-r border-white/10 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Toko Pojok Jepara" class="h-10 w-auto object-contain rounded-lg shadow-[0_0_15px_rgba(220,38,38,0.5)]">
                        <div>
                            <div class="text-xl font-black text-red-500 tracking-wide">TOKO POJOK</div>
                            <div class="text-[10px] text-gray-400 tracking-widest leading-none">ADMIN</div>
                        </div>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white">Admin Panel</h2>
                <p class="text-gray-400 text-sm">Toko Pojok Jepara</p>
            </div>
            
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-semibold">Dashboard</span>
                </a>

                <a href="{{ route('admin.produk.index') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.produk.*') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-semibold">Produk</span>
                </a>

                <a href="{{ route('admin.stok.index') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.stok.*') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="font-semibold">Stok</span>
                </a>

                <a href="{{ route('admin.pesanan.index') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.pesanan.*') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span class="font-semibold">Pesanan</span>
                </a>

                <a href="{{ route('admin.clustering.index') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.clustering.*') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <span class="font-semibold">K-Means Clustering</span>
                </a>

                <a href="{{ route('admin.laporan.index') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.laporan.*') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="font-semibold">Laporan</span>
                </a>

                <a href="{{ route('admin.notifications') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 {{ request()->routeIs('admin.notifications') ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white shadow-lg shadow-red-500/30' : 'hover:bg-white/5' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="font-semibold">Notifikasi</span>
                </a>

                <div class="border-t border-white/10 my-4"></div>

                <a href="{{ route('home') }}" class="group flex items-center px-4 py-3 text-gray-300 hover:text-white rounded-xl transition-all duration-300 hover:bg-white/5">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="font-semibold">Kembali ke Toko</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="backdrop-blur-xl bg-gray-900/60 border-b border-white/10">
                <div class="flex justify-between items-center px-4 lg:px-8 py-5">
                    <div class="flex items-center gap-4 animate-slide-right">
                        <!-- Mobile sidebar toggle -->
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all" aria-label="Toggle sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-black text-white">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')
                            <p class="text-gray-400 mt-1">@yield('subtitle')</p>
                        @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-3 px-4 py-2 backdrop-blur-xl bg-white/5 border border-white/10 rounded-full">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg shadow-red-500/30">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-white font-semibold">{{ Auth::user()->name }}</span>
                        </div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-full transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="fixed top-24 right-8 z-50 animate-slide-up max-w-md">
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
                <div class="fixed top-24 right-8 z-50 animate-slide-up max-w-md">
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
                <div class="fixed top-24 right-8 z-50 animate-slide-up max-w-md">
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
                <div class="fixed top-24 right-8 z-50 animate-slide-up max-w-md">
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
                <div class="fixed top-24 right-8 z-50 animate-slide-up max-w-md">
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
            <main class="flex-1 p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // CSRF Token for AJAX requests
        window.csrfToken = '{{ csrf_token() }}';

        // Toggle admin sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelectorAll('.fixed.top-24').forEach(el => {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                });
            }, 5000);
        });

        // Notification helper function
        function showNotification(message, type = 'success') {
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
            notification.className = 'fixed top-24 right-8 z-50 animate-slide-up max-w-md';
            notification.innerHTML = `
                <div class="backdrop-blur-xl ${colors[type]} border px-6 py-4 rounded-2xl shadow-2xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${icons[type]}
                        </svg>
                        <p class="flex-1 text-sm font-medium">${message}</p>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="hover:opacity-70 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.transition = 'opacity 0.5s';
                notification.style.opacity = '0';
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
                    showNotification('Anda tidak memiliki akses untuk melakukan tindakan ini.', 'error');
                } else if (status === 404) {
                    showNotification('Data tidak ditemukan.', 'error');
                } else if (status === 422) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        const firstError = Object.values(errors)[0][0];
                        showNotification(firstError, 'error');
                    } else {
                        showNotification('Data yang Anda masukkan tidak valid.', 'error');
                    }
                } else if (status >= 500) {
                    showNotification('Terjadi kesalahan server. Silakan coba lagi nanti.', 'error');
                } else {
                    showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            } else {
                showNotification('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'error');
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
