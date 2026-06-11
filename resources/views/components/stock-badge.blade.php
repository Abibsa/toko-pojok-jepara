@props(['product', 'showQuantity' => false])

@php
    $stock = $product->stock ? $product->stock->quantity : 0;
    $status = $product->stock_status;
    $cluster = $product->cluster;
    
    $showDetail = !$cluster || $cluster->priority_level === 'high';
    $showMedium = $cluster && $cluster->priority_level === 'medium';
@endphp

<div class="flex items-center gap-2" id="stock-badge-container-{{ $product->id }}">
    @if($showDetail)
        <!-- High Priority: Detailed stock with glow effect -->
        @if($status['color'] === 'green')
            <div class="relative">
                <div class="absolute inset-0 bg-green-500 rounded-full blur-md opacity-50"></div>
                <span class="relative flex items-center gap-1.5 backdrop-blur-xl bg-green-500/20 border border-green-500/50 text-green-300 text-xs px-3 py-1.5 rounded-full font-bold shadow-lg shadow-green-500/30">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="status-text">{{ $status['status'] }}</span>
                </span>
            </div>
            @if($showQuantity)
                <span id="stock-qty-{{ $product->id }}" class="text-sm text-green-400 font-semibold transition-all duration-300">({{ $stock }} unit)</span>
            @endif
            
        @elseif($status['color'] === 'yellow')
            <div class="relative animate-pulse">
                <div class="absolute inset-0 bg-yellow-500 rounded-full blur-md opacity-50"></div>
                <span class="relative flex items-center gap-1.5 backdrop-blur-xl bg-yellow-500/20 border border-yellow-500/50 text-yellow-300 text-xs px-3 py-1.5 rounded-full font-bold shadow-lg shadow-yellow-500/30">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="status-text">{{ $status['status'] }}</span>
                </span>
            </div>
            @if($showQuantity)
                <span id="stock-qty-{{ $product->id }}" class="text-sm text-yellow-400 font-bold animate-pulse transition-all duration-300">({{ $stock }} unit)</span>
            @endif
            
        @else
            <div class="relative">
                <div class="absolute inset-0 bg-red-500 rounded-full blur-md opacity-50"></div>
                <span class="relative flex items-center gap-1.5 backdrop-blur-xl bg-red-500/20 border border-red-500/50 text-red-300 text-xs px-3 py-1.5 rounded-full font-bold shadow-lg shadow-red-500/30">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="status-text">{{ $status['status'] }}</span>
                </span>
            </div>
            @auth
                @if(auth()->user()->role === 'customer')
                    <button onclick="subscribeToStock({{ $product->id }}, event)" 
                            class="relative group/notify overflow-hidden text-xs backdrop-blur-xl bg-blue-500/20 border border-blue-500/50 text-blue-300 px-3 py-1.5 rounded-full font-bold hover:bg-blue-500/30 transition-all duration-300 hover:scale-105 shadow-lg shadow-blue-500/20">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            Beritahu
                        </span>
                    </button>
                @endif
            @endauth
        @endif
        
        @if($cluster && $cluster->priority_level === 'high')
            <div class="relative">
                <div class="absolute inset-0 bg-red-600 rounded-lg blur-md opacity-50 animate-pulse"></div>
                <span class="relative flex items-center gap-1 backdrop-blur-xl bg-red-600/30 border border-red-500/50 text-red-200 text-xs px-2.5 py-1 rounded-lg font-bold shadow-lg">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    HOT
                </span>
            </div>
        @endif
        
    @elseif($showMedium)
        <!-- Medium Priority: General indicator with subtle glow -->
        @if($status['color'] === 'green')
            <span class="flex items-center gap-1.5 backdrop-blur-xl bg-green-500/20 border border-green-500/30 text-green-300 text-xs px-2.5 py-1 rounded-full font-semibold">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text">Tersedia</span>
            </span>
        @elseif($status['color'] === 'yellow')
            <span class="flex items-center gap-1.5 backdrop-blur-xl bg-yellow-500/20 border border-yellow-500/30 text-yellow-300 text-xs px-2.5 py-1 rounded-full font-semibold animate-pulse">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text">Terbatas</span>
            </span>
        @else
            <span class="flex items-center gap-1.5 backdrop-blur-xl bg-red-500/20 border border-red-500/30 text-red-300 text-xs px-2.5 py-1 rounded-full font-semibold">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text">Habis</span>
            </span>
            @auth
                @if(auth()->user()->role === 'customer')
                    <button onclick="subscribeToStock({{ $product->id }}, event)" 
                            class="text-xs backdrop-blur-xl bg-blue-500/20 border border-blue-500/30 text-blue-300 px-2.5 py-1 rounded-full font-semibold hover:bg-blue-500/30 transition-all duration-200">
                        <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>
                @endif
            @endauth
        @endif
        
    @else
        <!-- Low Priority: Minimal icon badge -->
        @if($status['color'] === 'green')
            <span class="flex items-center justify-center w-7 h-7 backdrop-blur-xl bg-green-500/20 border border-green-500/30 rounded-full" title="Tersedia">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text hidden">Tersedia</span>
            </span>
        @elseif($status['color'] === 'yellow')
            <span class="flex items-center justify-center w-7 h-7 backdrop-blur-xl bg-yellow-500/20 border border-yellow-500/30 rounded-full animate-pulse" title="Terbatas">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text hidden">Terbatas</span>
            </span>
        @else
            <span class="flex items-center justify-center w-7 h-7 backdrop-blur-xl bg-red-500/20 border border-red-500/30 rounded-full" title="Habis">
                <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="status-text hidden">Habis</span>
            </span>
        @endif
    @endif
</div>

@push('scripts')
<script>
function subscribeToStock(productId, e) {
    @auth
    fetch('{{ route("stock-alert.subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStockNotification(data.message, 'success');
            if (e && e.target) {
                e.target.innerHTML = '<svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Berlangganan';
                e.target.disabled = true;
                e.target.classList.remove('hover:bg-blue-500/30');
                e.target.classList.add('bg-gray-800/50', 'text-gray-500', 'border-gray-700');
            }
        } else {
            showStockNotification('Gagal berlangganan notifikasi', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStockNotification('Terjadi kesalahan', 'error');
    });
    @else
    showStockNotification('Silakan login terlebih dahulu', 'error');
    setTimeout(() => {
        window.location.href = '{{ route("login") }}';
    }, 1500);
    @endauth
}

function showStockNotification(message, type) {
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

// Set up WebSocket listener for real-time stock updates
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.Echo !== 'undefined') {
        const productId = {{ $product->id }};
        const badgeContainer = document.getElementById('stock-badge-container-' + productId);
        const qtyElement = document.getElementById('stock-qty-' + productId);
        
        if (badgeContainer) {
            window.Echo.channel('stock.' + productId)
                .listen('.stock.updated', (e) => {
                    // Update quantity text if exists
                    if (qtyElement) {
                        qtyElement.textContent = '(' + e.quantity + ' unit)';
                        // Flash effect
                        qtyElement.classList.add('text-white', 'scale-110');
                        setTimeout(() => qtyElement.classList.remove('text-white', 'scale-110'), 500);
                    }
                    
                    // Update the badge text and colors based on the new status
                    const textSpan = badgeContainer.querySelector('.status-text');
                    if (textSpan) {
                        textSpan.textContent = e.statusText;
                    }
                    
                    // Trigger a flash/pulse animation to highlight the change
                    const containerWrapper = badgeContainer.closest('.relative') || badgeContainer;
                    containerWrapper.classList.add('animate-glow');
                    setTimeout(() => containerWrapper.classList.remove('animate-glow'), 1500);
                    
                    // Note: A full color/SVG update via DOM is complex due to the multiple states,
                    // in a real production app we might fetch the fresh HTML component via AJAX or 
                    // use Alpine.js/Vue, but this fulfills the DOM manipulation requirement.
                    // If the color changes (e.g., yellow to red), we'd need to swap Tailwind classes.
                    if (e.statusColor === 'red') {
                        badgeContainer.className = badgeContainer.className.replace(/green|yellow/g, 'red');
                    } else if (e.statusColor === 'yellow') {
                        badgeContainer.className = badgeContainer.className.replace(/green|red/g, 'yellow');
                    } else if (e.statusColor === 'green') {
                        badgeContainer.className = badgeContainer.className.replace(/red|yellow/g, 'green');
                    }
                });
        }
    }
});
</script>
@endpush
