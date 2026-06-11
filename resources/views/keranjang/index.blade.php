<x-app-layout>
    <x-slot name="title">Keranjang Belanja</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6 animate-fade-in">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Beranda</a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-white font-medium">Keranjang Belanja</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-black text-white mb-2">Keranjang Belanja</h1>
            <p class="text-gray-400">Kelola produk yang akan Anda beli</p>
        </div>

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden">
                        <div class="px-6 py-4 border-b border-white/10">
                            <h2 class="text-lg font-bold text-white">
                                Produk dalam Keranjang ({{ $totalItems }} item)
                            </h2>
                        </div>

                        <div class="divide-y divide-white/10">
                            @foreach($cartItems as $item)
                                <div class="p-6 hover:bg-white/5 transition-colors duration-200" data-cart-id="{{ $item->id }}">
                                    <div class="flex items-start sm:items-center gap-4 flex-col sm:flex-row">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <img src="{{ $item->product->image_url }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 loading="lazy"
                                                 class="w-20 h-20 object-cover rounded-xl border border-white/10">
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-white">
                                                <a href="{{ route('produk.show', $item->product->slug) }}" class="hover:text-red-400 transition-colors">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-400">{{ $item->product->category->name }}</p>
                                            
                                            <!-- Price Info -->
                                            <div class="mt-2">
                                                @if($item->hasPriceChanged())
                                                    @php $priceChange = $item->price_change; @endphp
                                                    <div class="mb-2 p-2 rounded-lg {{ $priceChange['type'] == 'increase' ? 'bg-yellow-500/10 border border-yellow-500/30' : 'bg-green-500/10 border border-green-500/30' }}">
                                                        <p class="text-xs font-semibold {{ $priceChange['type'] == 'increase' ? 'text-yellow-400' : 'text-green-400' }}">
                                                            ⚠️ Harga telah berubah!
                                                        </p>
                                                        <p class="text-xs text-gray-300 mt-1">
                                                            <span class="line-through">Rp {{ number_format($priceChange['old_price'], 0, ',', '.') }}</span>
                                                            → 
                                                            <span class="font-bold">Rp {{ number_format($priceChange['new_price'], 0, ',', '.') }}</span>
                                                            <span class="{{ $priceChange['type'] == 'increase' ? 'text-yellow-400' : 'text-green-400' }}">
                                                                ({{ $priceChange['type'] == 'increase' ? '+' : '' }}{{ $priceChange['percentage'] }}%)
                                                            </span>
                                                        </p>
                                                    </div>
                                                @endif

                                                @if($item->quantity >= 5)
                                                    <p class="text-sm text-green-400 font-medium">
                                                        💰 Harga Grosir: {{ $item->product->formatted_wholesale_price }}
                                                    </p>
                                                @else
                                                    <p class="text-sm text-gray-400">
                                                        Harga: {{ $item->product->formatted_price }}
                                                    </p>
                                                    @if($item->product->wholesale_price < $item->product->price)
                                                        <p class="text-xs text-blue-400">
                                                            Beli ≥5 untuk harga grosir: {{ $item->product->formatted_wholesale_price }}
                                                        </p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Quantity & Actions -->
                                        <div class="flex flex-col items-end space-y-3">
                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-2" data-item-id="{{ $item->id }}">
                                                <button onclick="adjustQty({{ $item->id }}, -1)" 
                                                        class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all {{ $item->quantity <= 1 ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>−</button>
                                                <input type="number" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock ? $item->product->stock->quantity : 0 }}"
                                                       class="qty-input w-16 text-center bg-gray-800/50 border border-white/10 rounded-xl py-1.5 text-white font-bold focus:outline-none focus:ring-2 focus:ring-red-500/50"
                                                       data-cart-id="{{ $item->id }}"
                                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                                <button onclick="adjustQty({{ $item->id }}, 1)" 
                                                        class="w-9 h-9 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all"
                                                        {{ $item->product->stock && $item->quantity >= $item->product->stock->quantity ? 'disabled' : '' }}>+</button>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-white" id="subtotal-{{ $item->id }}">
                                                    {{ $item->formatted_subtotal }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $item->formatted_price_used }} × <span class="qty-display" data-cart-id="{{ $item->id }}">{{ $item->quantity }}</span>
                                                </p>
                                            </div>

                                            <!-- Remove Button -->
                                            <button onclick="removeFromCart({{ $item->id }})" 
                                                    class="text-red-400 hover:text-red-300 text-sm font-medium flex items-center gap-1 transition-colors" aria-label="Hapus produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-white mb-4">Ringkasan Pesanan</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Total Item</span>
                                <span class="font-medium text-white" id="total-items">{{ $totalItems }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal</span>
                                <span class="font-medium text-white" id="cart-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-white/10 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-white">Total</span>
                                    <span class="text-lg font-black bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent" id="final-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wholesale Info -->
                        <div class="backdrop-blur-xl bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-300">Info Harga Grosir</h3>
                                    <p class="text-xs text-blue-400/80 mt-1">
                                        Beli ≥5 unit per produk untuk mendapatkan harga grosir otomatis
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <a href="{{ route('checkout.index') }}" 
                               class="w-full bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white py-3 px-4 rounded-xl font-bold hover:scale-105 transition-all duration-300 text-center block shadow-lg shadow-red-500/30">
                                Lanjut ke Checkout
                            </a>
                            <a href="{{ route('produk.index') }}" 
                               class="w-full bg-white/5 border border-white/10 text-gray-300 py-3 px-4 rounded-xl font-medium hover:bg-white/10 hover:border-white/20 transition-all duration-300 text-center block">
                                Lanjut Belanja
                            </a>
                            <button onclick="clearCart()" 
                                    class="w-full text-red-400 hover:text-red-300 py-2 text-sm font-medium transition-colors">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-12 text-center animate-fade-in">
                <div class="inline-flex p-6 rounded-2xl bg-white/5 border border-white/10 mb-6">
                    <svg class="w-20 h-20 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Keranjang Anda Kosong</h2>
                <p class="text-gray-400 mb-8">Belum ada produk yang ditambahkan ke keranjang</p>
                <a href="{{ route('produk.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Dynamic quantity adjustment (fixes stale onclick values - Bug #16)
        function adjustQty(cartId, delta) {
            const input = document.querySelector(`.qty-input[data-cart-id="${cartId}"]`);
            if (!input) return;
            let newVal = parseInt(input.value) + delta;
            const max = parseInt(input.max) || 999;
            if (newVal < 1) newVal = 1;
            if (newVal > max) newVal = max;
            input.value = newVal;
            updateQuantity(cartId, newVal);
        }

        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) return;
            
            fetch('{{ route("keranjang.update") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart_id: cartId, quantity: parseInt(newQuantity) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`subtotal-${cartId}`).textContent = data.subtotal;
                    document.getElementById('cart-total').textContent = data.total;
                    document.getElementById('final-total').textContent = data.total;
                    document.getElementById('total-items').textContent = data.total_items;
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) cartCount.textContent = data.total_items;

                    // Update qty display
                    const qtyDisplay = document.querySelector(`.qty-display[data-cart-id="${cartId}"]`);
                    if (qtyDisplay) qtyDisplay.textContent = parseInt(newQuantity);

                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                    // Reload to get correct values
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat memperbarui keranjang', 'error');
            });
        }

        function removeFromCart(cartId) {
            if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) return;
            
            fetch('{{ route("keranjang.remove") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart_id: cartId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-cart-id="${cartId}"]`).remove();
                    document.getElementById('cart-total').textContent = data.total;
                    document.getElementById('final-total').textContent = data.total;
                    document.getElementById('total-items').textContent = data.total_items;
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) cartCount.textContent = data.total_items;
                    showNotification(data.message, 'success');
                    if (data.total_items == 0) setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menghapus produk', 'error');
            });
        }

        function clearCart() {
            if (!confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?')) return;
            
            fetch('{{ route("keranjang.clear") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification('Gagal mengosongkan keranjang', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
            });
        }
    </script>
    @endpush
</x-app-layout>