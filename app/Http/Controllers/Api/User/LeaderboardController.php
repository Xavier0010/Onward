<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * GET /api/user/leaderboard?type=xp|streak — Global leaderboard with podium
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->query('type', 'xp');

        $friendIds = $user->getFriendIds();
        $allIds = array_merge([$user->id], $friendIds);

        $allUsers = User::whereIn('id', $allIds)
            ->where('role', 'user')
            ->select('id', 'username', 'first_name', 'last_name', 'avatar', 'streak_count')
            ->get();

        if ($type === 'streak') {
            $ranked = $allUsers->sortByDesc('streak_count')->values();
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
            $weekStart = now()->startOfWeek();
            $ranked = $allUsers->map(function ($u) use ($weekStart, $user) {
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

            $ranked = $ranked->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });
        }

        // Separate podium (top 3) and rest
        $podium = $ranked->take(3)->values();
        $rest = $ranked->slice(3)->values();

        // Find current user rank
        $currentUserRank = $ranked->firstWhere('is_current_user', true);

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'podium' => $podium,
                'rankings' => $rest,
                'current_user_rank' => $currentUserRank,
            ],
        ]);
    }
}
