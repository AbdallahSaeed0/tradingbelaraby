<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\NotificationPayload;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List notifications for the authenticated user.
     * GET /api/notifications?unread=0|1&page=...
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->notifications();
        if ($request->has('unread') && $request->boolean('unread')) {
            $query->whereNull('read_at');
        }
        $notifications = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 20));
        $locale = $request->get('locale', $request->header('Accept-Language', 'en'));
        $locale = str_starts_with($locale, 'ar') ? 'ar' : 'en';

        $items = $notifications->getCollection()->map(fn ($n) => $this->formatNotification($n, $locale));
        $notifications->setCollection($items);

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * POST /api/notifications/{id}/read  (mark one as read)
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * POST /api/notifications/read-all
     */
    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * DELETE /api/notifications/{id}
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();
        return response()->json(['success' => true]);
    }

    private function formatNotification($notification, string $locale): array
    {
        $data = $notification->data;
        $key = $data['key'] ?? $data['type'] ?? 'unknown';
        $title = NotificationPayload::titleForLocale($data, $locale);
        $body = NotificationPayload::bodyForLocale($data, $locale);
        $action = $data['action'] ?? ['type' => 'none', 'value' => '', 'meta' => []];

        return [
            'id' => $notification->id,
            'key' => $key,
            'title' => $title,
            'body' => $body,
            'action' => $action,
            'priority' => $data['priority'] ?? 'normal',
            'created_at' => $notification->created_at->toIso8601String(),
            'read_at' => $notification->read_at?->toIso8601String(),
        ];
    }
}
