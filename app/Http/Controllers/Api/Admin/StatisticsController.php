<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Todo;


class StatisticsController extends Controller
{
    public function statistics() {
        $totalUsers = User::where('role', 'user')->count();
        $totalTasksCreated = Todo::count();
        $totalTasksCompleted = Todo::where('status', 3)->count();
        
        $activeUsers = User::where('last_login_date', '>=', now()->subDays(7)->toDateString())->count();
        
        $highestStreak = (User::max('best_streak') ?? User::max('streak_count')) ?? 0;
        $averageStreak = round(User::where('role', 'user')->avg('streak_count') ?? 0, 1);
        
        // Growth (last 7 days)
        $growth = User::where('role', 'user')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();
            
        $userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $userGrowth[$date] = $growth[$date] ?? 0;
        }
            
        // Priority
        $priorityChart = [
            'Low' => Todo::where('priority', 1)->count(),
            'Medium' => Todo::where('priority', 2)->count(),
            'High' => Todo::where('priority', 3)->count(),
        ];

        // Activity Heatmap (tasks completed per day for last 7 days)
        $activity = Todo::where('status', 3)
            ->where('completed_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();

        $activityHeatmap = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $activityHeatmap[$date] = $activity[$date] ?? 0;
        }
        
        $mostUnlocked = \App\Models\Achievement::withCount('users')->orderByDesc('users_count')->first();
        $rarest = \App\Models\Achievement::withCount('users')->orderBy('users_count')->first();
        $averageAchievements = $totalUsers > 0 ? round(\App\Models\UserAchievement::count() / $totalUsers, 1) : 0;

        return response()->json([
            "success" => true,
            "data" => [
                "totalUsers" => $totalUsers,
                "totalTasksCreated" => $totalTasksCreated,
                "totalTasksCompleted" => $totalTasksCompleted,
                "activeUsers" => $activeUsers,
                "highestStreak" => $highestStreak,
                "averageStreak" => $averageStreak,
                "userGrowth" => $userGrowth,
                "priorityChart" => $priorityChart,
                "activityHeatmap" => $activityHeatmap,
                "mostUnlocked" => $mostUnlocked,
                "rarest" => $rarest,
                "averageAchievements" => $averageAchievements
            ]
        ]);
    }
}
