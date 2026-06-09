<?php

namespace App\Http\Controllers\Api\User;

use App\Models\AppNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/user/notifications — All notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = AppNotification::with('fromUser:id,username,first_name,last_name,avatar')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'data' => $n->data,
                    'read_at' => $n->read_at,
                    'from_user' => $n->fromUser ? [
                        'id' => $n->fromUser->id,
                        'username' => $n->fromUser->username,
                        'full_name' => $n->fromUser->first_name . ' ' . $n->fromUser->last_name,
                        'avatar' => $n->fromUser->avatar,
                    ] : null,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * POST /api/user/notifications/{id}/read — Mark as read
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = AppNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * GET /api/user/notifications/unread-count — Count unread
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = AppNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
        ]);
    }
}
