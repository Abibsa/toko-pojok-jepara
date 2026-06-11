@extends('layouts.app')

@section('content')

<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('pesanan.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Pesanan
            </a>
        </div>

        @php
            $statusConfig = [
                'pending' => [
                    'bg' => 'from-yellow-600 to-orange-500',
                    'text' => 'text-yellow-400',
                    'border' => 'border-yellow-500/30',
                    'shadow' => 'shadow-yellow-500/20',
                    'label' => 'Menunggu Konfirmasi'
                ],
                'confirmed' => [
                    'bg' => 'from-blue-600 to-cyan-500',
                    'text' => 'text-blue-400',
                    'border' => 'border-blue-500/30',
                    'shadow' => 'shadow-blue-500/20',
                    'label' => 'Dikonfirmasi'
                ],
                'processing' => [
                    'bg' => 'from-purple-600 to-pink-500',
                    'text' => 'text-purple-400',
                    'border' => 'border-purple-500/30',
                    'shadow' => 'shadow-purple-500/20',
                    'label' => 'Sedang Diproses'
                ],
                'shipped' => [
                    'bg' => 'from-indigo-600 to-blue-500',
                    'text' => 'text-indigo-400',
                    'border' => 'border-indigo-500/30',
                    'shadow' => 'shadow-indigo-500/20',
                    'label' => 'Sedang Dikirim'
                ],
                'completed' => [
                    'bg' => 'from-green-600 to-emerald-500',
                    'text' => 'text-green-400',
                    'border' => 'border-green-500/30',
                    'shadow' => 'shadow-green-500/20',
                    'label' => 'Selesai'
                ],
                'cancelled' => [
                    'bg' => 'from-red-600 to-rose-500',
                    'text' => 'text-red-400',
                    'border' => 'border-red-500/30',
                    'shadow' => 'shadow-red-500/20',
                    'label' => 'Dibatalkan'
                ]
            ];
            $config = $statusConfig[$order->status] ?? $statusConfig['pending'];
        @endphp

        <!-- Order Header -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-8 mb-6 animate-fade-in">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-black text-white mb-2">{{ $order->order_code }}</h1>
                    <p class="text-sm text-gray-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-bold {{ $config['text'] }} bg-white/5 border {{ $config['border'] }}">
                    {{ $config['label'] }}
                </span>
            </div>

            <!-- Order Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-500 mb-1">Metode Pembayaran</p>
                    <p class="text-sm font-semibold text-white">{{ ucfirst($order->payment_method) }}</p>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
                    <p class="text-sm font-semibold text-white">{{ ucfirst($order->payment_status) }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-6 animate-fade-in" style="animation-delay: 0.1s;">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Alamat Pengiriman
            </h2>
            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                <p class="text-sm font-semibold text-white mb-1">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-400">{{ $order->shipping_address }}</p>
                @if($order->user->phone)
                    <p class="text-sm text-gray-400 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        {{ $order->user->phone }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden mb-6 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-lg font-bold text-white">Produk yang Dipesan</h2>
            </div>
            <div class="p-6 space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-200">
                        <img src="{{ $item->product->image_url }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-20 h-20 object-cover rounded-lg border border-white/10 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-white mb-1">{{ $item->product->name }}</h4>
                            <p class="text-sm text-gray-400 mb-2">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                            @if($item->quantity >= 5)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">
                                    Harga Grosir
                                </span>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold text-white">{{ $item->formatted_subtotal }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pickup Info (if applicable) -->
        @if($order->pickup_method === 'pickup' && $order->status === 'menunggu_diambil')
        <div class="backdrop-blur-xl bg-orange-500/10 border border-orange-500/30 rounded-2xl p-6 relative overflow-hidden mb-6">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-32 h-32 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
            </div>
            
            <h3 class="text-xl font-bold text-orange-400 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Informasi Pengambilan (BOPIS)
            </h3>
            
            <div class="space-y-4 text-sm text-orange-200/80">
                <div class="flex gap-3">
                    <div class="font-semibold text-orange-300 w-32">Alamat Toko:</div>
                    <div class="flex-1">Toko Pojok Jepara<br>Jl. Raya Pojok No. 123, Jepara</div>
                </div>
                <div class="flex gap-3">
                    <div class="font-semibold text-orange-300 w-32">Batas Waktu:</div>
                    <div class="flex-1 text-white font-bold" id="pickup-countdown">
                        {{ $order->pickup_deadline ? $order->pickup_deadline->format('d M Y, H:i') : '-' }}
                    </div>
                </div>

                {{-- Estimation Section --}}
                <div class="mt-6 pt-4 border-t border-orange-500/20">
                    <h4 class="font-semibold text-orange-300 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status Persiapan Pesanan
                    </h4>
                    
                    @if($order->ready_at)
                        <div class="p-4 bg-green-500/20 border border-green-500/30 rounded-xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 text-green-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-green-400 font-bold text-lg">Pesanan Sudah Siap! 🎉</p>
                                <p class="text-green-300/80 text-xs">Silakan ambil pesanan Anda di toko sekarang.</p>
                            </div>
                        </div>
                    @elseif($order->estimated_ready_at)
                        <div class="p-4 bg-orange-500/10 border border-orange-500/20 rounded-xl relative overflow-hidden">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <p class="text-xs text-orange-300/70 mb-1">Estimasi Siap Pada</p>
                                    <p class="text-xl font-black text-white" id="estimation-time">
                                        {{ $order->estimated_ready_at->format('H:i') }} WIB
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-orange-300/70 mb-1">Sisa Waktu</p>
                                    <p class="text-lg font-bold text-orange-400" id="prep-countdown">Menghitung...</p>
                                </div>
                            </div>
                            
                            {{-- Progress Bar --}}
                            <div class="w-full h-2 bg-orange-900/30 rounded-full overflow-hidden mt-3">
                                <div id="prep-progress" class="h-full bg-gradient-to-r from-orange-500 to-yellow-400 rounded-full transition-all duration-1000" style="width: 0%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="text-orange-400/80 mt-4 border-t border-orange-500/20 pt-4">
                    <p>Silakan tunjukkan halaman ini atau sebutkan ID Pesanan <strong>{{ $order->order_code }}</strong> kepada kasir saat mengambil pesanan.</p>
                </div>
            </div>
        </div>
        
        @push('scripts')
        <script>
            @if($order->pickup_deadline)
            // Simple countdown for pickup deadline
            function updateCountdown() {
                const deadline = new Date("{{ $order->pickup_deadline->toIso8601String() }}").getTime();
                const now = new Date().getTime();
                const diff = deadline - now;
                
                const el = document.getElementById('pickup-countdown');
                if (diff < 0) {
                    el.innerHTML = '<span class="text-red-500">Waktu pengambilan telah habis</span>';
                    return;
                }
                
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                
                el.innerHTML = `Sisa waktu: ${hours} jam ${minutes} menit (<span class="font-normal">{{ $order->pickup_deadline->format('d M Y, H:i') }}</span>)`;
            }
            updateCountdown();
            setInterval(updateCountdown, 60000);
            @endif

            @if($order->estimated_ready_at && !$order->ready_at)
            // Preparation Estimation Countdown & Progress Bar
            function updatePrepCountdown() {
                const createdAt = new Date("{{ $order->created_at->toIso8601String() }}").getTime();
                const estimatedAt = new Date("{{ $order->estimated_ready_at->toIso8601String() }}").getTime();
                const now = new Date().getTime();
                
                const totalDuration = estimatedAt - createdAt;
                const elapsed = now - createdAt;
                
                let progress = (elapsed / totalDuration) * 100;
                progress = Math.min(Math.max(progress, 0), 100); // Clamp between 0-100
                
                const progressBar = document.getElementById('prep-progress');
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                
                const diff = estimatedAt - now;
                const el = document.getElementById('prep-countdown');
                
                if (diff <= 0) {
                    if (el) el.innerHTML = 'Seharusnya Sudah Siap';
                    return;
                }
                
                const minutes = Math.floor(diff / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                if (el) {
                    if (minutes > 0) {
                        el.innerHTML = `${minutes}m ${seconds}s`;
                    } else {
                        el.innerHTML = `${seconds}s`;
                    }
                }
            }
            updatePrepCountdown();
            setInterval(updatePrepCountdown, 1000);
            @endif
        </script>
        @endpush
        @endif

        <!-- Order Summary -->
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.3s;">
            <h2 class="text-lg font-bold text-white mb-4">Ringkasan Pembayaran</h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Subtotal ({{ $order->orderItems->count() }} produk)</span>
                    <span class="text-white font-semibold">{{ $order->formatted_total }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Ongkos Kirim</span>
                    <span class="text-white font-semibold">Gratis</span>
                </div>
                <div class="pt-3 border-t border-white/10">
                    <div class="flex justify-between items-center">
                        <span class="text-white font-bold">Total Pembayaran</span>
                        <span class="text-2xl font-black {{ $config['text'] }}">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            @if($order->note)
                <div class="mt-6 p-4 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-xs text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-300">{{ $order->note }}</p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        @if(in_array($order->status, ['pending', 'confirmed']))
            <div class="mt-6 animate-fade-in" style="animation-delay: 0.4s;">
                <button onclick="cancelOrder('{{ $order->order_code }}', this)" 
                        class="w-full px-6 py-3 bg-white/5 border border-white/10 text-gray-300 rounded-xl font-bold hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                    Batalkan Pesanan
                </button>
            </div>
        @endif

        @if($order->status === 'shipped' && $order->pickup_method === 'delivery')
            <div class="mt-6 animate-fade-in" style="animation-delay: 0.4s;">
                <button onclick="completeOrder('{{ $order->order_code }}', this)" 
                        class="w-full px-6 py-3 bg-green-500/20 border border-green-500/30 text-green-400 rounded-xl font-bold hover:bg-green-500/30 hover:border-green-500/50 transition-all duration-300 shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Pesanan Diterima (Selesai)
                    </span>
                </button>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    function cancelOrder(orderCode, btn) {
        if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
            return;
        }

        // Disable button and show loading
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Membatalkan...</span>';
        }

        fetch(`/pesanan/${orderCode}/batal`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.href = '{{ route("pesanan.index") }}', 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat membatalkan pesanan', 'error');
        });
    }

    function completeOrder(orderCode, btn) {
        if (!confirm('Apakah Anda yakin pesanan ini sudah Anda terima dengan baik?')) {
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyelesaikan...</span>';
        }

        fetch(`/pesanan/${orderCode}/selesai`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menyelesaikan pesanan', 'error');
            btn.disabled = false;
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-4 p-4 rounded-2xl shadow-2xl z-50 backdrop-blur-xl border animate-slide-up ${
            type === 'success' 
                ? 'bg-green-500/10 text-green-400 border-green-500/30 shadow-green-500/20' 
                : 'bg-red-500/10 text-red-400 border-red-500/30 shadow-red-500/20'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>
@endpush
