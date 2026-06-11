<x-app-layout>
    <x-slot name="title">Checkout</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6 animate-fade-in">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Beranda</a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('keranjang.index') }}" class="text-gray-400 hover:text-white transition-colors">Keranjang</a>
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-white font-medium">Checkout</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-black text-white mb-2">Checkout</h1>
            <p class="text-gray-400">Lengkapi informasi pesanan Anda</p>
        </div>

        <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pickup Method -->
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in">
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Metode Pengambilan
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex flex-col p-4 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 hover:border-red-500/30 transition-all duration-200 group">
                            <div class="flex items-center mb-2">
                                <input type="radio" name="pickup_method" value="delivery" class="text-red-500 focus:ring-red-500/50 bg-gray-800 border-gray-600" checked onchange="togglePickupMethod()">
                                <span class="ml-2 font-medium text-white group-hover:text-red-400 transition-colors">🚚 Kirim ke Alamat</span>
                            </div>
                            <span class="text-xs text-gray-400 ml-6">Barang akan dikirim ke alamat Anda</span>
                        </label>
                        
                        <label class="flex flex-col p-4 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 hover:border-red-500/30 transition-all duration-200 group">
                            <div class="flex items-center mb-2">
                                <input type="radio" name="pickup_method" value="pickup" class="text-red-500 focus:ring-red-500/50 bg-gray-800 border-gray-600" onchange="togglePickupMethod()">
                                <span class="ml-2 font-medium text-white group-hover:text-red-400 transition-colors">🏪 Ambil di Toko (BOPIS)</span>
                            </div>
                            <span class="text-xs text-gray-400 ml-6">Pesan online, ambil sendiri di toko fisik</span>
                        </label>
                    </div>

                    <!-- Info Pickup Store -->
                    <div id="store-pickup-info" class="hidden mt-4 bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-blue-300 mb-3">Informasi Pengambilan:</h3>
                        <div class="text-xs text-blue-400/80 space-y-2">
                            <p><strong>Lokasi:</strong> Toko Pojok Jepara - Jl. Raya Pojok No. 123, Jepara</p>
                            <p><strong>Batas Waktu:</strong> Anda memiliki waktu <strong>24 jam</strong> untuk mengambil pesanan setelah checkout.</p>
                        </div>

                        <!-- Estimasi Waktu Persiapan -->
                        <div class="mt-3 pt-3 border-t border-blue-500/20">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-cyan-300 font-medium">Estimasi Persiapan Pesanan</p>
                                    <p class="text-lg font-black text-white" id="estimated-time">~{{ $totalItems <= 5 ? '10-15' : ($totalItems <= 15 ? '15-25' : '25-40') }} menit</p>
                                </div>
                            </div>
                            <p class="text-xs text-blue-400/60">Pesanan Anda akan disiapkan segera setelah checkout. Anda akan mendapat informasi waktu yang lebih akurat di halaman pesanan.</p>
                        </div>

                        <p class="text-red-300 mt-3 text-xs">* Pesanan akan dibatalkan otomatis jika melewati batas waktu 24 jam.</p>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div id="shipping-address-container" class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.1s;">
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Informasi Pengiriman
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-300 mb-2">
                                Alamat Lengkap <span class="text-red-400">*</span>
                            </label>
                            <textarea id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3" 
                                      required
                                      placeholder="Masukkan alamat lengkap untuk pengiriman..."
                                      class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300">{{ auth()->user()->address }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Pastikan alamat sudah benar dan lengkap</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.1s;">
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Metode Pembayaran
                    </h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 hover:border-red-500/30 transition-all duration-200 group">
                            <input type="radio" name="payment_method" value="transfer" class="text-red-500 focus:ring-red-500/50 bg-gray-800 border-gray-600" checked>
                            <div class="ml-3">
                                <div class="font-medium text-white group-hover:text-red-400 transition-colors">Transfer Bank</div>
                                <div class="text-sm text-gray-400">Bayar melalui transfer ke rekening toko</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 hover:border-red-500/30 transition-all duration-200 group">
                            <input type="radio" name="payment_method" value="cod" class="text-red-500 focus:ring-red-500/50 bg-gray-800 border-gray-600">
                            <div class="ml-3">
                                <div class="font-medium text-white group-hover:text-red-400 transition-colors">Cash on Delivery (COD)</div>
                                <div class="text-sm text-gray-400">Bayar saat barang diterima</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.2s;">
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Catatan Pesanan
                    </h2>
                    
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-300 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea id="note" 
                                  name="note" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan khusus untuk pesanan ini..."
                                  class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-transparent transition-all duration-300"></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-white mb-4">Ringkasan Pesanan</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-3 py-2 border-b border-white/5 last:border-b-0">
                                <img src="{{ $item->product->image_url }}" 
                                     alt="{{ $item->product->name }}" 
                                     loading="lazy"
                                     class="w-10 h-10 object-cover rounded-lg border border-white/10">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->formatted_price_used }} × {{ $item->quantity }}
                                        @if($item->quantity >= 5)
                                            <span class="text-green-400 font-medium">(Grosir)</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-sm font-bold text-white">
                                    {{ $item->formatted_subtotal }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Item</span>
                            <span class="font-medium text-white">{{ $totalItems }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="font-medium text-white">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Ongkos Kirim</span>
                            <span class="font-medium text-green-400">Gratis</span>
                        </div>
                        <div class="border-t border-white/10 pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-bold text-white">Total</span>
                                <span class="text-lg font-black bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Validation Alert -->
                    <div id="stock-alert" class="hidden backdrop-blur-xl bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-300">Periksa Stok</h3>
                                <p class="text-xs text-yellow-400/80 mt-1">
                                    Kami akan memvalidasi ketersediaan stok sebelum memproses pesanan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button type="submit" 
                                id="checkout-btn"
                                class="w-full bg-gradient-to-r from-red-600 via-rose-500 to-orange-400 text-white py-3 px-4 rounded-xl font-bold hover:scale-105 transition-all duration-300 shadow-lg shadow-red-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <span id="checkout-text">Proses Pesanan</span>
                            <span id="checkout-loading" class="hidden flex items-center justify-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Memproses...
                            </span>
                        </button>
                        
                        <a href="{{ route('keranjang.index') }}" 
                           class="w-full bg-white/5 border border-white/10 text-gray-300 py-3 px-4 rounded-xl font-medium hover:bg-white/10 hover:border-white/20 transition-all duration-300 text-center block">
                            Kembali ke Keranjang
                        </a>
                    </div>

                    <!-- Payment Info -->
                    <div class="mt-6 backdrop-blur-xl bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                        <h3 class="text-sm font-medium text-blue-300 mb-2">Informasi Pembayaran</h3>
                        <div class="text-xs text-blue-400/80 space-y-1">
                            <p><strong>Transfer Bank:</strong></p>
                            <p>BCA: 1234567890</p>
                            <p>a.n. Toko Pojok Jepara</p>
                            <p class="mt-2 text-blue-300">Konfirmasi pembayaran via WhatsApp setelah transfer</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target; // Fix #3: capture form reference properly
            const btn = document.getElementById('checkout-btn');
            const btnText = document.getElementById('checkout-text');
            const btnLoading = document.getElementById('checkout-loading');
            
            // Disable button and show loading
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Validate stock first
            fetch('{{ route("checkout.validate-stock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    let errorMessage = 'Masalah dengan stok:\n';
                    data.errors.forEach(error => {
                        errorMessage += '• ' + error + '\n';
                    });
                    
                    if (data.suggestions && data.suggestions.length > 0) {
                        errorMessage += '\nSaran produk alternatif:\n';
                        data.suggestions.forEach(suggestion => {
                            errorMessage += '• ' + suggestion.product + ': ' + suggestion.alternatives.join(', ') + '\n';
                        });
                    }
                    
                    alert(errorMessage);
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    return;
                }
                
                // Stock valid, proceed with checkout
                const formData = new FormData(form); // Fix #3: use form reference instead of this
                
                fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    } else {
                        showNotification(data.message, 'error');
                        
                        if (data.errors) {
                            let errorMessage = 'Masalah dengan pesanan:\n';
                            data.errors.forEach(error => {
                                errorMessage += '• ' + error + '\n';
                            });
                            alert(errorMessage);
                        }
                        
                        btn.disabled = false;
                        btnText.classList.remove('hidden');
                        btnLoading.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat memproses pesanan', 'error');
                    btn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                });
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat validasi stok', 'error');
                btn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            });
        });

        // Show stock alert on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('stock-alert').classList.remove('hidden');
        });

        // Toggle Pickup Method
        function togglePickupMethod() {
            const pickupMethod = document.querySelector('input[name="pickup_method"]:checked').value;
            const shippingContainer = document.getElementById('shipping-address-container');
            const storeInfo = document.getElementById('store-pickup-info');
            const addressInput = document.getElementById('shipping_address');
            
            if (pickupMethod === 'pickup') {
                shippingContainer.classList.add('hidden');
                storeInfo.classList.remove('hidden');
                addressInput.removeAttribute('required');
            } else {
                shippingContainer.classList.remove('hidden');
                storeInfo.classList.add('hidden');
                addressInput.setAttribute('required', 'required');
            }
        }
    </script>
    @endpush
</x-app-layout>