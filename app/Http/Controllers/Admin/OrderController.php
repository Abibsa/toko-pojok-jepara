<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems'])->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);
        
        return view('admin.pesanan.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product.stock']);
        return view('admin.pesanan.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,completed,cancelled,menunggu_diambil'
        ]);

        if ($validated['status'] === 'shipped' && $order->status !== 'shipped') {
            $validated['shipped_at'] = now();
        }

        $order->update($validated);

        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }

    public function markPickedUp(Request $request, Order $order)
    {
        if ($order->status !== 'menunggu_diambil') {
            return back()->with('error', 'Status pesanan tidak valid untuk diambil');
        }

        $order->update([
            'status' => 'completed',
            'payment_status' => 'paid', // Assuming picked up implies payment is completed
            'note' => ($order->note ? $order->note . ' | ' : '') . 'Diambil oleh customer pada ' . now()->format('d/m/Y H:i')
        ]);

        return back()->with('success', 'Pesanan telah ditandai sebagai sudah diambil dan selesai.');
    }

    public function updateEstimation(Request $request, Order $order)
    {
        $validated = $request->validate([
            'estimated_minutes' => 'required|integer|min:5|max:180'
        ]);

        $order->update([
            'estimated_ready_at' => now()->addMinutes($validated['estimated_minutes'])
        ]);

        return back()->with('success', 'Estimasi waktu persiapan berhasil diperbarui menjadi ' . $validated['estimated_minutes'] . ' menit.');
    }

    public function markReady(Order $order)
    {
        if ($order->pickup_method !== 'pickup') {
            return back()->with('error', 'Pesanan ini bukan pesanan ambil di toko.');
        }

        $order->update([
            'ready_at' => now(),
            'note' => ($order->note ? $order->note . ' | ' : '') . 'Pesanan siap diambil pada ' . now()->format('d/m/Y H:i')
        ]);

        return back()->with('success', 'Pesanan telah ditandai siap diambil oleh customer.');
    }
}
