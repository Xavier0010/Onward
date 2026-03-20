<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Todo;
use App\Models\User;
use App\Services\AchievementService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserTodoController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        $query = Todo::where('user_id', $user->id);

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('started')) {
            $query->where('start_date', '<=', now());
        }

        if ($request->has('not_ended')) {
            $query->where('end_date', '>=', now());
        }

        $todos = $query->get();

        return response()->json([
            "success" => true,
            "data" => $todos
        ]);
    }

    public function store(Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            "task" => "required|string|max:255",
            "description" => "nullable|string",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
            "priority" => "required|in:1,2,3",
            "status" => "required|in:1,2,3",
        ]);

        $todo = Todo::create([
            "user_id" => $user->id,
            "task" => $validated['task'],
            "description" => $validated['description'],
            "start_date" => $validated['start_date'],
            "end_date" => $validated['end_date'],
            "priority" => $validated['priority'],
            "status" => $validated['status'],
        ]);

        $achievementService = new AchievementService;
        $achievementService->check($user, 'tasks_created');

        return response()->json([
            "success" => true,
            "message" => "New to-do created successfully!",
            "data" => $todo
        ], 201);
    }

    public function update(Request $request, $id) {
        $user = $request->user();
        $todo = Todo::where("id", $id)->where("user_id", $user->id)->firstOrFail();

        $validated = $request->validate([
            "task" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "start_date" => "sometimes|date",
            "end_date" => "sometimes|date|after_or_equal:start_date",
            "priority" => "sometimes|in:1,2,3",
            "status" => "sometimes|in:1,2,3"
        ]);

        $todo->update($validated);

        if ($request->status == 3) {
            $achievementService = new AchievementService;
            $achievementService->check($user, 'tasks_completed');
        }

        return response()->json([
            "success" => true,
            "message" => "Todo updated successfully!",
            "data" => $todo
        ]);
    }

    public function destroy(Request $request, $id) {
            $user = $request->user();
            $todo = Todo::where("id", $id)->where("user_id", $user->id)->firstOrFail();
            
            $todo->delete();
            
            return response()->json([
                "success" => true,
                "message" => "Todo deleted successfully!",
                "data" => $todo
            ]);
    }
}
