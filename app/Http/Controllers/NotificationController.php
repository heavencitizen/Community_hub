<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Halaman Daftar Notifikasi Pengguna
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all');

        $query = $user->appNotifications()->with('sender');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Tandai notifikasi sebagai dibaca dan buka link tujuan
     */
    public function read(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotificationsCount(),
            ]);
        }

        return redirect($notification->link ?? route('notifications.index'));
    }

    /**
     * Tandai semua notifikasi pengguna sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        auth()->user()->appNotifications()->where('is_read', false)->update(['is_read' => true]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
