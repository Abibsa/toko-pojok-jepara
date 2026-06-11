@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('subtitle', 'Ubah detail produk')

@section('content')
<div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-6 max-w-2xl mx-auto">
    <form action="{{ route('admin.produk.update', $produk->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Produk</label>
            <input type="text" name="name" value="{{ $produk->name }}" required class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
            <select name="category_id" required class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:ring-2 focus:ring-red-500/50">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $produk->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Harga</label>
                <input type="number" name="price" value="{{ $produk->price }}" required class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Harga Grosir</label>
                <input type="number" name="wholesale_price" value="{{ $produk->wholesale_price }}" required class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Satuan (Unit)</label>
            <input type="text" name="unit" value="{{ $produk->unit }}" required class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300">{{ $produk->description }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-300 mb-2">Gambar Produk</label>
            @if($produk->image)
                <div class="mb-3">
                    <img src="{{ $produk->image_url }}" alt="{{ $produk->name }}" class="w-32 h-32 object-cover rounded-xl border border-white/10">
                    <p class="text-xs text-gray-500 mt-2">Gambar saat ini</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-500/20 file:text-red-400 hover:file:bg-red-500/30">
            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, WEBP (Max: 2MB) - Kosongkan jika tidak ingin mengubah</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.produk.index') }}" class="px-6 py-3 bg-white/5 border border-white/10 text-gray-300 rounded-xl hover:bg-white/10">Batal</a>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl font-bold shadow-lg shadow-red-500/30">Update Produk</button>
        </div>
    </form>
</div>
@endsection
