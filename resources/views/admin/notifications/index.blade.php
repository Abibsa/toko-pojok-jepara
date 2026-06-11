@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('subtitle', 'Kelola dan monitor notifikasi sistem')

@section('content')

    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="px-4 py-2 backdrop-blur-xl bg-white/5 border border-white/10 rounded-xl">
                <span class="text-sm text-gray-400">Total Notifikasi: </span>
                <span class="text-lg font-bold text-white">{{ $notifications->total() }}</span>
            </div>
        </div>
        
        @if($notifications->count() > 0)
            <button onclick="markAllAsRead()" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-bold hover:from-cyan-500 hover:to-blue-600 transition-all duration-300 shadow-lg shadow-blue-500/30 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tandai Semua Dibaca
            </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden animate-fade-in">
        @if($notifications->count() > 0)
            <div class="divide-y divide-white/10">
                @foreach($notifications as $notification)
                    @php
                        $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                        $isUnread = is_null($notification->read_at);
                        
                        // Determine notification type and icon
                        $type = $data['type'] ?? 'info';
                        $typeConfig = [
                            'success' => [
                                'bg' => 'from-green-600 to-emerald-500',
                                'text' => 'text-green-400',
                                'border' => 'border-green-500/30',
                                'shadow' => 'shadow-green-500/20',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                            ],
                            'warning' => [
                                'bg' => 'from-yellow-600 to-orange-500',
                                'text' => 'text-yellow-400',
                                'border' => 'border-yellow-500/30',
                                'shadow' => 'shadow-yellow-500/20',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>'
                            ],
                            'error' => [
                                'bg' => 'from-red-600 to-rose-500',
                                'text' => 'text-red-400',
                                'border' => 'border-red-500/30',
                                'shadow' => 'shadow-red-500/20',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                            ],
                            'info' => [
                                'bg' => 'from-blue-600 to-cyan-500',
                                'text' => 'text-blue-400',
                                'border' => 'border-blue-500/30',
                                'shadow' => 'shadow-blue-500/20',
                                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                            ]
                        ];
                        $config = $typeConfig[$type] ?? $typeConfig['info'];
                    @endphp

                    <div class="p-6 hover:bg-white/5 transition-all duration-300 {{ $isUnread ? 'bg-white/5' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="p-3 rounded-xl bg-gradient-to-r {{ $config['bg'] }} shadow-lg {{ $config['shadow'] }}">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $config['icon'] !!}
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <h4 class="text-base font-bold text-white mb-1">
                                            {{ $data['title'] ?? 'Notifikasi' }}
                                            @if($isUnread)
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30">
                                                    Baru
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-300">
                                            {{ $data['message'] ?? 'Tidak ada pesan' }}
                                        </p>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        @if($isUnread)
                                            <button onclick="markAsRead('{{ $notification->id }}')" 
                                                    class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300"
                                                    title="Tandai dibaca">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <button onclick="deleteNotification('{{ $notification->id }}')" 
                                                class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all duration-300"
                                                title="Hapus notifikasi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Metadata -->
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if(!$isUnread)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Dibaca {{ $notification->read_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Additional Info -->
                                @if(isset($data['action_url']))
                                    <div class="mt-3">
                                        <a href="{{ $data['action_url'] }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold {{ $config['text'] }} bg-white/5 border {{ $config['border'] }} rounded-lg hover:bg-white/10 transition-all duration-300">
                                            Lihat Detail
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="p-6 border-t border-white/10">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="inline-flex p-6 rounded-2xl bg-white/5 border border-white/10 mb-6">
                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-gray-400">Anda tidak memiliki notifikasi saat ini</p>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    function markAsRead(notificationId) {
        fetch(`/notifikasi/${notificationId}/read`, {
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
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan', 'error');
        });
    }

    function deleteNotification(notificationId) {
        if (!confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) return;
        
        fetch(`/notifikasi/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan', 'error');
        });
    }

    function markAllAsRead() {
        fetch('/notifikasi/read-all', {
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
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan', 'error');
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
