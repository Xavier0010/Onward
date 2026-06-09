<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use App\Services\StreakService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        $streakService = new StreakService;
        $streakService->update($user);
        $user->refresh();

        $userFirstName = $user->first_name;
        $userCurrentStreak = $user->streak_count;
        $userStreakRank = User::where('streak_count', '>', $user->streak_count)->count() + 1;
        $userCompletionCount = Todo::where('user_id', $user->id)->where('status', 3)->count();
        $userCompletionRank = User::select(
            'users.id',
            DB::raw('COUNT(todos.id) as tasks_completed')
        )->join('todos', 'users.id', '=', 'todos.user_id')->where('todos.status', 3)->groupBy('users.id')->havingRaw('COUNT(todos.id) > ?', [$userCompletionCount])->count() + 1;

        // Stats
        $totalTasks = Todo::where('user_id', $user->id)->count();
        $completedTasks = $userCompletionCount;
        $activeTasks = Todo::where('user_id', $user->id)->whereIn('status', [1, 2])->count();
        $highPriority = Todo::where('user_id', $user->id)->where('priority', 3)->count();

        // Total XP (all time, including streak bonuses)
        $taskXp = Todo::where('user_id', $user->id)
            ->where('status', 3)
            ->get()
            ->sum(function ($todo) {
                switch ((int) $todo->priority) {
                    case 1: return 10;
                    case 2: return 15;
                    case 3: return 20;
                    default: return 10;
                }
            });

        $streakXp = 0;
        if ($user->streak_count >= 30) $streakXp += 200;
        if ($user->streak_count >= 7) $streakXp += 50;
        
        $totalXp = $taskXp + $streakXp;
        $spentXp = $user->spent_xp ?? 0;
        $availableXp = $totalXp - $spentXp;

        // Weekly XP
        $weekStart = now()->startOfWeek();
        $weeklyTaskXp = Todo::where('user_id', $user->id)
            ->where('status', 3)
            ->where('completed_at', '>=', $weekStart)
            ->get()
            ->sum(function ($todo) {
                switch ((int) $todo->priority) {
                    case 1: return 10;
                    case 2: return 15;
                    case 3: return 20;
                    default: return 10;
                }
            });
        $weeklyStreakXp = 0;
        if ($user->streak_count >= 30) $weeklyStreakXp += 200;
        if ($user->streak_count >= 7) $weeklyStreakXp += 50;
        $weeklyXp = $weeklyTaskXp + $weeklyStreakXp;

        return response()->json([
            "success" => true,
            "data" => [
                "first_name" => $userFirstName,
                "current_streak" => $userCurrentStreak,
                "streak_rank" => $userStreakRank,
                "completion_rank" => $userCompletionRank,
                "total_tasks" => $totalTasks,
                "completed_tasks" => $completedTasks,
                "active_tasks" => $activeTasks,
                "high_priority" => $highPriority,
                "total_xp" => $totalXp,
                "available_xp" => $availableXp,
                "weekly_xp" => $weeklyXp,
            ]
        ]);
    }
}
