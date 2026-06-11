@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('subtitle', 'Pesanan ' . $order->order_code)

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Order Details -->
    <div class="md:col-span-2 space-y-6">
        <div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">Informasi Pelanggan</h3>
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-300">
                <div><span class="block text-gray-500 mb-1">Nama</span> {{ $order->user->name }}</div>
                <div><span class="block text-gray-500 mb-1">Email</span> {{ $order->user->email }}</div>
                <div class="col-span-2">
                    <span class="block text-gray-500 mb-1">Metode & Alamat Pengiriman</span> 
                    @if($order->pickup_method === 'pickup')
                        <span class="inline-block px-2 py-1 bg-orange-500/20 text-orange-400 text-xs rounded-md border border-orange-500/30 mb-2">🏪 Ambil di Toko (BOPIS)</span><br>
                    @endif
                    {{ $order->shipping_address }}
                </div>
            </div>
        </div>

        <div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">Produk Pesanan</h3>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    @php
                        $currentStock = $item->product->stock ? $item->product->stock->quantity : 0;
                        $stockStatus = $item->product->stock_status;
                    @endphp
                    <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <img src="{{ $item->product->image_url }}" class="w-16 h-16 rounded-xl object-cover border border-white/10">
                                <div>
                                    <h4 class="text-white font-bold">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-gray-400">{{ $item->quantity }} x {{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-white font-bold">{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Stock Detail Section --}}
                        <div class="mt-3 pt-3 border-t border-white/5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-xs text-gray-500">Stok Saat Ini</span>
                                    </div>
                                    <span class="text-sm font-bold text-{{ $stockStatus['color'] }}-400">
                                        {{ $currentStock }} {{ $item->product->unit ?? 'pcs' }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full font-medium
                                    @if($stockStatus['color'] === 'green')
                                        bg-green-500/15 text-green-400 border border-green-500/30
                                    @elseif($stockStatus['color'] === 'yellow')
                                        bg-yellow-500/15 text-yellow-400 border border-yellow-500/30
                                    @else
                                        bg-red-500/15 text-red-400 border border-red-500/30
                                    @endif
                                ">
                                    {{ $stockStatus['badge'] }}
                                </span>
                            </div>

                            {{-- Stock bar indicator --}}
                            <div class="mt-2">
                                @php
                                    $maxDisplay = max($currentStock, 100);
                                    $percentage = $maxDisplay > 0 ? min(($currentStock / $maxDisplay) * 100, 100) : 0;
                                @endphp
                                <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        @if($stockStatus['color'] === 'green') bg-gradient-to-r from-green-500 to-emerald-400
                                        @elseif($stockStatus['color'] === 'yellow') bg-gradient-to-r from-yellow-500 to-amber-400
                                        @else bg-gradient-to-r from-red-500 to-rose-400
                                        @endif
                                    " style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>

                            {{-- Stock vs ordered comparison --}}
                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                <span>Dipesan: <strong class="text-gray-300">{{ $item->quantity }}</strong></span>
                                <span>•</span>
                                <span>Sisa Stok: <strong class="text-{{ $stockStatus['color'] }}-400">{{ $currentStock }}</strong></span>
                                @if($currentStock < $item->quantity && $order->status === 'pending')
                                    <span class="text-red-400 font-medium">⚠ Stok tidak mencukupi!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Stock Alert Summary --}}
            @php
                $lowStockItems = $order->orderItems->filter(function($item) {
                    $qty = $item->product->stock ? $item->product->stock->quantity : 0;
                    return $qty <= 10;
                });
            @endphp
            @if($lowStockItems->count() > 0)
                <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                    <div class="flex items-center gap-2 text-yellow-400 text-sm font-medium mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span>Peringatan Stok</span>
                    </div>
                    <p class="text-xs text-gray-400">
                        {{ $lowStockItems->count() }} produk dalam pesanan ini memiliki stok rendah atau habis. 
                        Pastikan ketersediaan sebelum memproses pesanan.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Status Management -->
    <div class="space-y-6">
        <div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">Ringkasan</h3>
            <div class="flex justify-between items-center py-2 border-b border-white/10">
                <span class="text-gray-400">Status Saat Ini</span>
                <span class="font-bold text-{{ $order->status_color }}-400">{{ $order->status_badge }}</span>
            </div>
            <div class="flex justify-between items-center py-4 font-bold text-xl">
                <span class="text-white">Total Akhir</span>
                <span class="text-green-400">{{ $order->formatted_total }}</span>
            </div>
        </div>

        <div class="backdrop-blur-xl bg-gray-900/70 border border-white/10 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">Ubah Status</h3>
            @if(session('success'))
                <div class="mb-4 text-sm text-green-400 bg-green-500/10 p-3 rounded-lg">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST">
                @csrf @method('PATCH')
                <select name="status" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 mb-4 focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="menunggu_diambil" {{ $order->status == 'menunggu_diambil' ? 'selected' : '' }}>Menunggu Diambil (BOPIS)</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-xl shadow-lg hover:from-cyan-500 hover:to-blue-600 transition-all">Simpan Perubahan Status</button>
            </form>

            @if($order->status === 'menunggu_diambil' && $order->pickup_method === 'pickup')
            <div class="mt-6 border-t border-white/10 pt-6">
                <h4 class="text-orange-400 font-bold mb-2">Aksi Khusus BOPIS</h4>
                <p class="text-sm text-gray-400 mb-4">Batas Pengambilan: <strong class="text-white">{{ $order->pickup_deadline ? $order->pickup_deadline->format('d/m/Y H:i') : '-' }}</strong></p>
                <form action="{{ route('admin.pesanan.picked-up', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-xl shadow-lg hover:from-red-500 hover:to-orange-500 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tandai Sudah Diambil
                    </button>
                </form>
            </div>
            @endif
        </div>
        @if($order->pickup_method === 'pickup')
        <div class="backdrop-blur-xl bg-orange-900/40 border border-orange-500/30 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-orange-400 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Estimasi Pengambilan
            </h3>
            
            <div class="mb-4 p-3 bg-white/5 rounded-xl border border-white/10">
                <p class="text-xs text-gray-400 mb-1">Status Persiapan</p>
                <p class="font-bold {{ $order->ready_at ? 'text-green-400' : 'text-orange-400' }}">
                    {{ $order->estimated_ready_label }}
                </p>
                @if($order->ready_at)
                    <p class="text-xs text-gray-500 mt-1">Siap sejak: {{ $order->ready_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>

            @if(!$order->ready_at && in_array($order->status, ['pending', 'confirmed', 'menunggu_diambil']))
                <form action="{{ route('admin.pesanan.update-estimation', $order) }}" method="POST" class="mb-4">
                    @csrf @method('PATCH')
                    <label class="block text-xs text-gray-400 mb-2">Ubah Estimasi Waktu (Menit)</label>
                    <div class="flex gap-2">
                        <input type="number" name="estimated_minutes" min="5" max="180" 
                               value="{{ $order->estimated_ready_at ? max(0, $order->estimated_ready_at->diffInMinutes(now())) : '' }}"
                               class="w-full px-3 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-orange-500" required>
                        <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white font-bold rounded-xl transition-colors">
                            Update
                        </button>
                    </div>
                </form>

                <form action="{{ route('admin.pesanan.mark-ready', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-600 to-emerald-500 text-white font-bold rounded-xl shadow-lg hover:from-emerald-500 hover:to-green-600 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tandai Pesanan Siap
                    </button>
                </form>
            @endif
        </div>
        @endif
        
        <a href="{{ route('admin.pesanan.index') }}" class="block text-center w-full py-3 bg-white/5 border border-white/10 text-gray-300 rounded-xl hover:bg-white/10 transition-all">Kembali ke Daftar</a>
    </div>
</div>
@endsection
