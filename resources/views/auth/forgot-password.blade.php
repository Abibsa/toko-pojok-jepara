@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Lupa Password</h2>

        <p class="text-gray-600 mb-6 text-sm">
            Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
        </p>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Kirim Link Reset Password
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm">Kembali ke Login</a>
            </div>
        </form>
    </div>
</div>
@endsection
