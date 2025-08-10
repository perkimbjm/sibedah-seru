<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = Auth::user();
        $query = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter read/unread
        $filter = request('filter'); // all|unread|read
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        // Filter by type
        if ($type = request('type')) {
            $query->byType($type);
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('notification.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        // Hanya pemilik notifikasi yang boleh menandai dibaca
        if ($notification->notifiable_id !== $user->id || $notification->notifiable_type !== \App\Models\User::class) {
            abort(403, 'Anda tidak dapat mengakses notifikasi ini.');
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai telah dibaca.'
        ]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai telah dibaca.'
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    public function getRecentNotifications()
    {
        $user = Auth::user();
        Log::info('User ID:', ['user_id' => $user->id]);
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        Log::info('Recent notifications:', ['notifications' => $notifications->toArray()]);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        // Hanya pemilik notifikasi yang boleh menghapus
        if ($notification->notifiable_id !== $user->id || $notification->notifiable_type !== \App\Models\User::class) {
            abort(403, 'Anda tidak dapat menghapus notifikasi ini.');
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus.'
        ]);
    }

    public function clearAll()
    {
        $user = Auth::user();
        Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil dihapus.'
        ]);
    }
}
