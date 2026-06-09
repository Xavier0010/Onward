<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Friendship;
use App\Models\AppNotification;
use App\Models\ActivityEvent;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * GET /api/user/friends — List accepted friends
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');

        $friendIds = $user->getFriendIds();

        $query = User::whereIn('id', $friendIds)
            ->select('id', 'username', 'first_name', 'last_name', 'avatar', 'streak_count');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $friends = $query->get()->map(function ($friend) {
            return [
                'id' => $friend->id,
                'username' => $friend->username,
                'first_name' => $friend->first_name,
                'last_name' => $friend->last_name,
                'full_name' => $friend->first_name . ' ' . $friend->last_name,
                'avatar' => $friend->avatar,
                'streak_count' => $friend->streak_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $friends,
        ]);
    }

    /**
     * GET /api/user/friends/search?q= — Search users to add
     */
    public function search(Request $request)
    {
        $user = $request->user();
        $query = $request->query('q', '');

        $friendIds = $user->getFriendIds();

        // Get IDs of users with pending requests (either direction)
        $pendingSentIds = Friendship::where('sender_id', $user->id)
            ->where('status', 'pending')
            ->pluck('receiver_id')->toArray();
        $pendingReceivedIds = Friendship::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->pluck('sender_id')->toArray();

        $excludeIds = array_merge([$user->id], $friendIds, $pendingSentIds, $pendingReceivedIds);

        $dbQuery = User::whereNotIn('id', $excludeIds)->where('role', 'user');

        if (strlen($query) >= 2) {
            $dbQuery->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%");
            });
        }

        $users = $dbQuery->inRandomOrder()->limit(10)->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'username' => $u->username,
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'full_name' => $u->first_name . ' ' . $u->last_name,
                    'avatar' => $u->avatar,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * POST /api/user/friends/request — Send friend request
     */
    public function sendRequest(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        $receiverId = $request->receiver_id;

        // Prevent self-request
        if ($user->id === (int) $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send friend request to yourself.',
            ], 422);
        }

        // Prevent duplicate (either direction)
        $exists = Friendship::where(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($user, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $user->id);
        })->whereIn('status', ['pending', 'accepted'])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Friend request already exists or you are already friends.',
            ], 409);
        }

        $friendship = Friendship::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        // Create notification for receiver
        AppNotification::create([
            'user_id' => $receiverId,
            'from_user_id' => $user->id,
            'type' => 'friend_request',
            'data' => [
                'friendship_id' => $friendship->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' sent you a friend request.',
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Friend request sent!',
            'data' => $friendship,
        ], 201);
    }

    /**
     * POST /api/user/friends/{id}/accept — Accept incoming request
     */
    public function acceptRequest(Request $request, $id)
    {
        $user = $request->user();

        $friendship = Friendship::where('id', $id)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        // Create activity event
        ActivityEvent::create([
            'user_id' => $user->id,
            'type' => 'friend_accepted',
            'data' => [
                'friend_id' => $friendship->sender_id,
                'friend_name' => $friendship->sender->first_name . ' ' . $friendship->sender->last_name,
            ],
        ]);

        // Notify the sender
        AppNotification::create([
            'user_id' => $friendship->sender_id,
            'from_user_id' => $user->id,
            'type' => 'friend_accept',
            'data' => [
                'message' => $user->first_name . ' ' . $user->last_name . ' accepted your friend request.',
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Friend request accepted!',
        ]);
    }

    /**
     * POST /api/user/friends/{id}/reject — Reject incoming request
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = $request->user();

        $friendship = Friendship::where('id', $id)
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Friend request rejected.',
        ]);
    }

    /**
     * DELETE /api/user/friends/request/{id} — Cancel outgoing request
     */
    public function cancelRequest(Request $request, $id)
    {
        $user = $request->user();

        $friendship = Friendship::where('id', $id)
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Friend request cancelled.',
        ]);
    }

    /**
     * DELETE /api/user/friends/{id} — Remove accepted friend
     */
    public function removeFriend(Request $request, $id)
    {
        $user = $request->user();

        $friendship = Friendship::where('id', $id)
            ->where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->firstOrFail();

        $friendship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Friend removed.',
        ]);
    }

    /**
     * GET /api/user/friends/pending — Incoming + outgoing pending requests
     */
    public function pendingRequests(Request $request)
    {
        $user = $request->user();

        $incoming = Friendship::with('sender:id,username,first_name,last_name,avatar')
            ->where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'user' => [
                        'id' => $f->sender->id,
                        'username' => $f->sender->username,
                        'first_name' => $f->sender->first_name,
                        'last_name' => $f->sender->last_name,
                        'full_name' => $f->sender->first_name . ' ' . $f->sender->last_name,
                        'avatar' => $f->sender->avatar,
                    ],
                    'created_at' => $f->created_at->diffForHumans(),
                ];
            });

        $outgoing = Friendship::with('receiver:id,username,first_name,last_name,avatar')
            ->where('sender_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'user' => [
                        'id' => $f->receiver->id,
                        'username' => $f->receiver->username,
                        'first_name' => $f->receiver->first_name,
                        'last_name' => $f->receiver->last_name,
                        'full_name' => $f->receiver->first_name . ' ' . $f->receiver->last_name,
                        'avatar' => $f->receiver->avatar,
                    ],
                    'created_at' => $f->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'incoming' => $incoming,
                'outgoing' => $outgoing,
                'incoming_count' => $incoming->count(),
                'total_count' => $incoming->count() + $outgoing->count(),
            ],
        ]);
    }

    /**
     * GET /api/user/friends/activity — Friend activity feed
     */
    public function activity(Request $request)
    {
        $user = $request->user();
        $friendIds = $user->getFriendIds();

        $events = ActivityEvent::with('user:id,username,first_name,last_name,avatar')
            ->whereIn('user_id', $friendIds)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'type' => $event->type,
                    'data' => $event->data,
                    'user' => [
                        'id' => $event->user->id,
                        'username' => $event->user->username,
                        'full_name' => $event->user->first_name . ' ' . $event->user->last_name,
                        'avatar' => $event->user->avatar,
                    ],
                    'created_at' => $event->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * GET /api/user/friends/leaderboard — Friend leaderboard (XP or streak)
     */
    public function leaderboard(Request $request)
    {
        $user = $request->user();
        $type = $request->query('type', 'xp'); // 'xp' or 'streak'
        $friendIds = $user->getFriendIds();
        $allIds = array_merge([$user->id], $friendIds);

        $users = User::whereIn('id', $allIds)
            ->select('id', 'username', 'first_name', 'last_name', 'avatar', 'streak_count')
            ->get();

        if ($type === 'streak') {
            $ranked = $users->sortByDesc('streak_count')->values();
            $ranked = $ranked->map(function ($u, $index) use ($user) {
                return [
                    'rank' => $index + 1,
                    'id' => $u->id,
                    'username' => $u->username,
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'full_name' => $u->first_name . ' ' . $u->last_name,
                    'avatar' => $u->avatar,
                    'value' => $u->streak_count,
                    'is_current_user' => $u->id === $user->id,
                ];
            });
        } else {
            // XP calculation
            $weekStart = now()->startOfWeek();
            $ranked = $users->map(function ($u) use ($weekStart, $user) {
                $taskXp = Todo::where('user_id', $u->id)
                    ->where('status', 3)
                    ->where('completed_at', '>=', $weekStart)
                    ->get()
                    ->sum(function ($todo) {
                        return match ((int) $todo->priority) {
                            1 => 10,
                            2 => 15,
                            3 => 20,
                            default => 10,
                        };
                    });

                $streakXp = 0;
                if ($u->streak_count >= 30) $streakXp += 200;
                if ($u->streak_count >= 7) $streakXp += 50;

                return [
                    'id' => $u->id,
                    'username' => $u->username,
                    'first_name' => $u->first_name,
                    'last_name' => $u->last_name,
                    'full_name' => $u->first_name . ' ' . $u->last_name,
                    'avatar' => $u->avatar,
                    'value' => $taskXp + $streakXp,
                    'is_current_user' => $u->id === $user->id,
                ];
            })->sortByDesc('value')->values();

            // Add rank
            $ranked = $ranked->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $ranked->values(),
        ]);
    }

    /**
     * GET /api/user/friends/streaks — Streak comparison
     */
    public function streaks(Request $request)
    {
        $user = $request->user();
        $friendIds = $user->getFriendIds();
        $allIds = array_merge([$user->id], $friendIds);

        $streaks = User::whereIn('id', $allIds)
            ->select('id', 'username', 'first_name', 'last_name', 'avatar', 'streak_count')
            ->orderByDesc('streak_count')
            ->get()
            ->map(function ($u) use ($user) {
                return [
                    'id' => $u->id,
                    'username' => $u->username,
                    'full_name' => $u->first_name . ' ' . $u->last_name,
                    'avatar' => $u->avatar,
                    'streak_count' => $u->streak_count,
                    'is_current_user' => $u->id === $user->id,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $streaks,
        ]);
    }
}
