<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function statistics() {
        $totalUsers = User::count();

        # Count users with more than 2 tasks
        $activeUsers = User::whereHas('todos', function($query) {
            $query->select('user_id')->groupBy('user_id')->havingRaw('COUNT(*) > 2');
        })->count();

        $highestStreak = User::max('streak_count');

        # Count active users with streak
        $activeStreakUsers = User::where('streak_count', '>', 0)->count();

        $streakLeaderboard = User::orderBy('streak_count', 'desc')->limit(10)->get(['id', 'username', 'streak_count']);

        $completionLeaderboard = User::select(
            'users.id',
            'users.username',
            DB::raw('COUNT(todos.id) as tasks_completed')
        )->join('todos', 'users.id', '=', 'todos.user_id')->where('todos.status', 3)->groupBy('users.id', 'users.username')->orderByDesc('tasks_completed')->limit(10)->get();

        return response()->json([
            "success" => true,
            "data" => [
                "total_users" => $totalUsers,
                "active_users" => $activeUsers,
                "highest_streak" => $highestStreak,
                "active_users_streak" => $activeStreakUsers,
                "streak_leaderboard" => $streakLeaderboard,
                "completion_leaderboard" => $completionLeaderboard
            ]
        ]);
    }
}
