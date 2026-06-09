<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Todo;
use App\Models\User;
use App\Models\ActivityEvent;
use App\Models\AppNotification;
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

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('todos', 'public');
            $todo->update([
                'file_path' => $path,
                'original_filename' => $request->file('file')->getClientOriginalName(),
            ]);
        }

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

        $wasNotCompleted = $todo->status != 3;

        $validated = $request->validate([
            "task" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "start_date" => "sometimes|date",
            "end_date" => "sometimes|date|after_or_equal:start_date",
            "priority" => "sometimes|in:1,2,3",
            "status" => "sometimes|in:1,2,3"
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('todos', 'public');
            $validated['file_path'] = $path;
            $validated['original_filename'] = $request->file('file')->getClientOriginalName();
        }

        $todo->update($validated);

        // If task was just completed, log activity + notify friends
        if ($wasNotCompleted && $request->status == 3) {
            $todo->update(['completed_at' => now()]);

            $achievementService = new AchievementService;
            $achievementService->check($user, 'tasks_completed');

            // Log activity event
            ActivityEvent::create([
                'user_id' => $user->id,
                'type' => 'task_completed',
                'data' => [
                    'task_name' => $todo->task,
                    'priority' => $todo->priority,
                ],
            ]);

            // Notify friends
            $friendIds = $user->getFriendIds();
            foreach ($friendIds as $friendId) {
                AppNotification::create([
                    'user_id' => $friendId,
                    'from_user_id' => $user->id,
                    'type' => 'task_completed',
                    'data' => [
                        'message' => $user->first_name . ' completed "' . $todo->task . '"',
                        'task_name' => $todo->task,
                    ],
                ]);
            }
        }

        return response()->json([
            "success" => true,
            "message" => "Todo updated successfully!",
            "data" => $todo
        ]);
    }

    /**
     * PUT /api/user/todo/{id}/toggle — Toggle task completion (used by Livewire)
     */
    public function toggle(Request $request, $id) {
        $user = $request->user();
        $todo = Todo::where("id", $id)->where("user_id", $user->id)->firstOrFail();

        if ($todo->status == 3) {
            // Uncomplete
            $todo->status = 1;
            $todo->completed_at = null;
            $todo->save();
        } else {
            // Complete
            $todo->status = 3;
            $todo->completed_at = now();
            $todo->save();

            $achievementService = new AchievementService;
            $achievementService->check($user, 'tasks_completed');

            // Log activity event
            ActivityEvent::create([
                'user_id' => $user->id,
                'type' => 'task_completed',
                'data' => [
                    'task_name' => $todo->task,
                    'priority' => $todo->priority,
                ],
            ]);

            // Notify friends
            $friendIds = $user->getFriendIds();
            foreach ($friendIds as $friendId) {
                AppNotification::create([
                    'user_id' => $friendId,
                    'from_user_id' => $user->id,
                    'type' => 'task_completed',
                    'data' => [
                        'message' => $user->first_name . ' completed "' . $todo->task . '"',
                    ],
                ]);
            }
        }

        return response()->json([
            "success" => true,
            "message" => "Task toggled!",
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
