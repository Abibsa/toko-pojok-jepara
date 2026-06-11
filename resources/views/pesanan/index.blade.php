@extends('layouts.app')

@section('content')

<div class="min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-black text-white mb-2">Pesanan Saya</h1>
            <p class="text-gray-400">Riwayat dan status pesanan Anda</p>
        </div>

        @if($orders->count() > 0)
            <!-- Orders List -->
            <div class="space-y-6">
                @foreach($orders as $order)
                    @php
                        $statusConfig = [
                            'pending' => [
                                'bg' => 'from-yellow-600 to-orange-500',
                                'text' => 'text-yellow-400',
                                'border' => 'border-yellow-500/30',
                                'shadow' => 'shadow-yellow-500/20',
                                'label' => 'Menunggu'
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
                                'label' => 'Diproses'
                            ],
                            'shipped' => [
                                'bg' => 'from-indigo-600 to-blue-500',
                                'text' => 'text-indigo-400',
                                'border' => 'border-indigo-500/30',
                                'shadow' => 'shadow-indigo-500/20',
                                'label' => 'Dikirim'
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

                    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden hover:border-white/20 transition-all duration-300 animate-fade-in">
                        <!-- Order Header -->
                        <div class="p-6 border-b border-white/10">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <h3 class="text-lg font-bold text-white">{{ $order->order_code }}</h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $config['text'] }} bg-white/5 border {{ $config['border'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                        @if($order->pickup_method === 'pickup' && in_array($order->status, ['pending', 'confirmed', 'menunggu_diambil']))
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $order->ready_at ? 'text-green-400 border-green-500/30 bg-green-500/10' : 'text-orange-400 border-orange-500/30 bg-orange-500/10' }} border">
                                                {{ $order->estimated_ready_label }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                                    <p class="text-2xl font-black {{ $config['text'] }}">{{ $order->formatted_total }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-6 space-y-3">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center gap-4 p-3 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-200">
                                    <img src="{{ $item->product->image_url }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-14 h-14 object-cover rounded-lg border border-white/10 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-white text-sm mb-1 truncate">{{ $item->product->name }}</h4>
                                        <p class="text-xs text-gray-400">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-bold text-white text-sm">{{ $item->formatted_subtotal }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Actions -->
                        <div class="p-6 pt-0">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('pesanan.show', $order->order_code) }}" 
                                   class="flex-1 px-6 py-3 bg-gradient-to-r {{ $config['bg'] }} text-white rounded-xl font-bold text-center hover:scale-105 transition-all duration-300 shadow-lg {{ $config['shadow'] }}">
                                    Lihat Detail
                                </a>
                                
                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <button onclick="cancelOrder('{{ $order->order_code }}', this)" 
                                            class="flex-1 px-6 py-3 bg-white/5 border border-white/10 text-gray-300 rounded-xl font-bold hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                                        Batalkan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-12 text-center animate-fade-in">
                <div class="inline-flex p-6 rounded-2xl bg-white/5 border border-white/10 mb-6">
                    <svg class="w-20 h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Belum Ada Pesanan</h3>
                <p class="text-gray-400 mb-8">Anda belum memiliki riwayat pesanan</p>
                <a href="{{ route('produk.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Mulai Belanja
                </a>
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
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat membatalkan pesanan', 'error');
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
