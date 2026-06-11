@extends('layouts.admin')

@section('title', 'Manajemen Stok')
@section('subtitle', 'Kelola dan monitor stok produk secara real-time')

@section('content')

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 tracking-wide">Total Produk</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $stats['total_products'] }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg shadow-blue-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 tracking-wide">Stok Kritis</p>
                    <p class="text-3xl font-black text-yellow-400 mt-1">{{ $stats['critical_stock'] }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-r from-yellow-600 to-orange-500 shadow-lg shadow-yellow-500/30 animate-pulse">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 animate-fade-in" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 tracking-wide">Stok Habis</p>
                    <p class="text-3xl font-black text-red-400 mt-1">{{ $stats['out_of_stock'] }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gradient-to-r from-red-600 to-rose-500 shadow-lg shadow-red-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-6 mb-8 animate-fade-in">
        <form method="GET" action="{{ route('admin.stok.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Cari Produk</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama produk..." 
                           class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                    <select name="category" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Status Stok</label>
                    <select name="filter" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('filter') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="critical" {{ request('filter') == 'critical' ? 'selected' : '' }}>Kritis (≤10)</option>
                        <option value="out_of_stock" {{ request('filter') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-xl font-bold hover:from-orange-500 hover:to-red-600 transition-all duration-300 shadow-lg shadow-red-500/30">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in">
        <div class="p-6 border-b border-white/10">
            <h3 class="text-xl font-bold text-white">Daftar Produk</h3>
            <p class="text-sm text-gray-400 mt-1">Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($products as $product)
                        <tr class="hover:bg-white/5 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-12 h-12 object-cover rounded-xl border border-white/10">
                                    <div>
                                        <div class="text-sm font-semibold text-white">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-400">SKU: {{ $product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $product->category->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-white">{{ $product->formatted_price }}</div>
                                <div class="text-xs text-gray-400">Grosir: {{ $product->formatted_wholesale_price }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-2xl font-black {{ ($product->stock->quantity ?? 0) <= 0 ? 'text-red-400' : (($product->stock->quantity ?? 0) <= 10 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $product->stock->quantity ?? 0 }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">{{ $product->unit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <x-stock-badge :product="$product" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="openUpdateModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock->quantity ?? 0 }})" 
                                            class="px-3 py-2 bg-blue-500/20 border border-blue-500/30 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-all duration-300 text-sm font-semibold">
                                        Update
                                    </button>
                                    <a href="{{ route('admin.stok.history', ['product_id' => $product->id]) }}" 
                                       class="px-3 py-2 bg-white/5 border border-white/10 text-gray-300 rounded-lg hover:bg-white/10 transition-all duration-300 text-sm font-semibold">
                                        History
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-lg font-semibold">Tidak ada produk ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-6 border-t border-white/10">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Update Stock Modal -->
    <div id="updateModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 w-96">
            <div class="backdrop-blur-xl bg-gray-900/90 border border-white/10 rounded-2xl shadow-2xl">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-white mb-6">Update Stok</h3>
                    <form id="updateForm" class="space-y-4">
                        <input type="hidden" id="updateProductId" name="product_id">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Produk</label>
                            <p id="updateProductName" class="text-sm text-white bg-white/5 border border-white/10 p-3 rounded-xl"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Stok Saat Ini</label>
                            <p id="updateCurrentStock" class="text-2xl font-black text-white"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Tipe</label>
                            <select id="updateType" name="type" class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                                <option value="in">Tambah Stok (Masuk)</option>
                                <option value="out">Kurangi Stok (Keluar)</option>
                                <option value="adjustment">Penyesuaian</option>
                            </select>
                        </div>

                        <div>
                            <label for="updateQuantity" class="block text-sm font-semibold text-gray-300 mb-2">Jumlah</label>
                            <input type="number" id="updateQuantity" name="quantity" min="1" required
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300">
                        </div>
                        
                        <div>
                            <label for="updateNote" class="block text-sm font-semibold text-gray-300 mb-2">Catatan</label>
                            <textarea id="updateNote" name="note" rows="3" placeholder="Catatan update stok..."
                                   class="w-full px-4 py-3 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all duration-300"></textarea>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="closeUpdateModal()" 
                                    class="flex-1 px-4 py-3 text-sm font-semibold text-gray-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-600 to-orange-500 rounded-xl hover:from-orange-500 hover:to-red-600 transition-all duration-300 shadow-lg shadow-red-500/30">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openUpdateModal(productId, productName, currentStock) {
        document.getElementById('updateProductId').value = productId;
        document.getElementById('updateProductName').textContent = productName;
        document.getElementById('updateCurrentStock').textContent = currentStock + ' unit';
        document.getElementById('updateQuantity').value = '';
        document.getElementById('updateNote').value = '';
        document.getElementById('updateModal').classList.remove('hidden');
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').classList.add('hidden');
    }

    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.stok.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeUpdateModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat update stok', 'error');
        });
    });

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
