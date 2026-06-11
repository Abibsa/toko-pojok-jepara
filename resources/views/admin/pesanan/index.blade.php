@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')
@section('subtitle', 'Pantau dan kelola semua transaksi')

@section('content')
    <div class="backdrop-blur-xl bg-gray-900/70 rounded-2xl border border-white/10 overflow-hidden">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">Daftar Pesanan</h3>
        </div>
        
        <div class="p-4 border-b border-white/10">
            <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex gap-4">
                <select name="status" class="px-4 py-2 bg-gray-800/50 border border-white/10 rounded-xl text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500/50">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="menunggu_diambil" {{ request('status') == 'menunggu_diambil' ? 'selected' : '' }}>Menunggu Diambil</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-xl shadow-lg transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">ID Pesanan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($orders as $order)
                        <tr class="hover:bg-white/5 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="text-white font-bold">{{ $order->order_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-white flex items-center gap-2">
                                    {{ $order->user->name ?? 'Guest' }}
                                    @if($order->pickup_method === 'pickup')
                                        <span title="Ambil di Toko" class="text-orange-400">🏪</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300 font-semibold">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-{{ $order->status_color }}-400">
                                {{ $order->status_badge }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.pesanan.show', $order) }}" class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded-lg font-bold hover:bg-blue-500/30">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-white/10">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
