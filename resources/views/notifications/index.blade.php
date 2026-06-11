<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8 animate-fade-in">
            <div>
                <h1 class="text-3xl font-black text-white mb-2">Notifikasi</h1>
                <p class="text-gray-400">Pemberitahuan terkait pesanan dan stok produk</p>
            </div>
            
            @if(count($notifications) > 0)
                <button type="button" onclick="markAllAsRead()" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-lg text-sm text-white transition-colors duration-300">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="space-y-4 animate-slide-up" id="notificationsList">
            @forelse($notifications as $notification)
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border {{ $notification->read_at ? 'border-white/5' : 'border-blue-500/30 bg-blue-500/5' }} p-6 transition-all duration-300" id="notification-{{ $notification->id }}">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg shadow-blue-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-lg font-bold text-white">{{ $notification->data['title'] ?? 'Pemberitahuan' }}</h3>
                                <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-300">{{ $notification->data['message'] ?? 'Anda memiliki notifikasi baru' }}</p>
                            
                            @if(isset($notification->data['product_id']))
                                <div class="mt-4">
                                    <a href="{{ route('produk.show', $notification->data['product_slug'] ?? $notification->data['product_id']) }}" class="text-blue-400 hover:text-blue-300 text-sm font-semibold underline">
                                        Lihat Produk
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        @if(!$notification->read_at)
                            <button onclick="markAsRead('{{ $notification->id }}')" class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition-colors" title="Tandai Dibaca">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-gray-400 text-lg">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function markAsRead(id) {
            fetch(`/notifikasi/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const el = document.getElementById(`notification-${id}`);
                    el.classList.remove('border-blue-500/30', 'bg-blue-500/5');
                    el.classList.add('border-white/5');
                    
                    // Hapus tombol centang
                    const btn = el.querySelector('button');
                    if (btn) btn.remove();
                }
            });
        }

        function markAllAsRead() {
            fetch(`/notifikasi/read-all`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
