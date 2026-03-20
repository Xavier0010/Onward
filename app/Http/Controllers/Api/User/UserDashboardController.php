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

        return response()->json([
            "success" => true,
            "data" => [
                "first_name" => $userFirstName,
                "current_streak" => $userCurrentStreak,
                "streak_rank" => $userStreakRank,
                "completion_rank" => $userCompletionRank
            ]
        ]);
    }
}
