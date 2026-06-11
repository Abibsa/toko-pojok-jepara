<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::user());
        
        return view('notifications.index', compact('notifications'));
    }

    public function admin()
    {
        
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $success = $this->notificationService->markNotificationAsRead($id, Auth::user());
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }

    public function destroy(Request $request, $id)
    {
        $success = $this->notificationService->deleteNotification($id, Auth::user());
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllNotificationsAsRead(Auth::user());
        
        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai dibaca'
        ]);
    }

    public function count()
    {
        $count = $this->notificationService->getNotificationCount(Auth::user());
        
        return response()->json([
            'count' => $count
        ]);
    }
}