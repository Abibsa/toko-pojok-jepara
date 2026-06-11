@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('subtitle', $produk->name)

@section('content')
<div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-8 max-w-3xl mx-auto">
    <div class="flex gap-6 mb-6">
        <img src="{{ $produk->image_url }}" class="w-32 h-32 object-cover rounded-xl border border-white/10">
        <div>
            <h2 class="text-3xl font-bold text-white mb-2">{{ $produk->name }}</h2>
            <div class="text-gray-400 mb-1">Kategori: {{ $produk->category->name }}</div>
            <div class="text-gray-400 mb-1">SKU: {{ $produk->id }}</div>
            <div class="mt-4 flex gap-4">
                <div class="px-4 py-2 bg-white/5 rounded-xl border border-white/10 text-white font-bold">{{ $produk->formatted_price }}</div>
                <div class="px-4 py-2 bg-yellow-500/10 rounded-xl border border-yellow-500/30 text-yellow-500 font-bold">Stok: {{ $produk->stock->quantity ?? 0 }} {{ $produk->unit }}</div>
            </div>
        </div>
    </div>
    <div class="bg-black/20 p-6 rounded-xl border border-white/5 mb-6">
        <h3 class="text-lg font-bold text-white mb-2">Deskripsi Produk</h3>
        <p class="text-gray-400 leading-relaxed">{{ $produk->description ?: 'Tidak ada deskripsi.' }}</p>
    </div>
    <a href="{{ route('admin.produk.index') }}" class="px-6 py-2 bg-white/5 border border-white/10 text-gray-300 rounded-xl hover:bg-white/10">Kembali</a>
</div>
@endsection
