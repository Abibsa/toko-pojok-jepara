@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <!-- Background Effects -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-10 w-72 h-72 bg-orange-600/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-red-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-in">
        <!-- Card -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 shadow-2xl">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-500 rounded-xl blur opacity-75"></div>
                    <div class="relative bg-gradient-to-r from-orange-600 via-rose-500 to-red-400 text-white px-6 py-3 rounded-xl font-bold text-2xl tracking-wide">
                        DAFTAR
                    </div>
                </div>
            </div>

            <h2 class="text-3xl font-black text-white mb-2 text-center">Buat Akun Baru</h2>
            <p class="text-gray-400 text-center mb-8">Bergabung dengan Toko Pojok Jepara</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300 @error('name') border-red-500/50 @enderror"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300 @error('email') border-red-500/50 @enderror"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300 @error('password') border-red-500/50 @enderror"
                        placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300"
                        placeholder="Ulangi password">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="group relative w-full overflow-hidden px-6 py-3 rounded-xl text-lg font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-red-500/50 mt-6">
                    <span class="absolute inset-0 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400"></span>
                    <span class="absolute inset-0 bg-gradient-to-r from-orange-400 via-rose-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    <span class="relative flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Daftar Sekarang
                    </span>
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <span class="text-gray-400">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="ml-2 text-red-400 hover:text-red-300 font-semibold transition-colors duration-300">
                    Login
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
