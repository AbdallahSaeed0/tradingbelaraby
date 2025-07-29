<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Get unread notifications count (for AJAX)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications->count();

        return response()->json([
            'count' => $count
        ]);
    }
}
