<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    // Index

    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $searchQuery = '';
    public $filterPriority = '';
    public $filterStatus = '';
    public $showTaskModal = false;
    public $showDetailsModal = false;

    public $selectedTask = null;

    public $quote = [
        'text' => 'Action is the foundational key to all success.',
        'author' => 'Pablo Picasso',
    ];

    public $leaderboard = [
        ['rank' => 1, 'name' => 'PixelNinja', 'xp' => 2840, 'medal' => 'gold', 'avatar' => 'PN', 'color' => '#22c55e'],
        ['rank' => 2, 'name' => 'CodeWizard', 'xp' => 2615, 'medal' => 'silver', 'avatar' => 'CW', 'color' => '#6366f1'],
        ['rank' => 3, 'name' => 'TaskMaster', 'xp' => 2390, 'medal' => 'bronze', 'avatar' => 'TM', 'color' => '#f59e0b'],
        ['rank' => 4, 'name' => 'ByteHunter', 'xp' => 2150, 'medal' => null, 'avatar' => 'BH', 'color' => '#ec4899'],
        ['rank' => 5, 'name' => 'DevStar99', 'xp' => 1980, 'medal' => null, 'avatar' => 'DS', 'color' => '#f97316'],
    ];

    /**
     * Reset pagination when filters change
     */
    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function updatingFilterPriority()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Main query
     */

    public function getStreakProperty()
    {
        return Auth::user()->streak_count;
    }

    public function getTasksQueryProperty()
    {
        return Todo::query()

            ->where('user_id', Auth::id())

            ->when($this->searchQuery, function ($query) {
                $query->where('task', 'like', '%' . $this->searchQuery . '%');
            })

            ->when($this->filterPriority, function ($query) {
                $query->where('priority', $this->filterPriority);
            })

            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })

            ->latest();
    }

    /**
     * Statistics
     */
    public function getTotalTasksProperty()
    {
        return Todo::where('user_id', Auth::id())->count();
    }

    public function getCompletedTasksProperty()
    {
        return Todo::where('user_id', Auth::id())
            ->where('status', 3)
            ->count();
    }

    public function getActiveTasksProperty()
    {
        return Todo::where('user_id', Auth::id())
            ->whereIn('status', [1, 2])
            ->count();
    }

    public function getHighPriorityProperty()
    {
        return Todo::where('user_id', Auth::id())
            ->where('priority', 3)
            ->count();
    }

    /**
     * Toggle complete status
     */
    public function toggleTask($taskId)
    {
        $task = Todo::where('user_id', Auth::id())
            ->findOrFail($taskId);

        if ($task->status == 3) {

            // back to pending
            $task->status = 1;
            $task->completed_at = null;

        } else {

            // completed
            $task->status = 3;
            $task->completed_at = now();
        }

        $task->save();
    }

    public function render()
    {
        return view('livewire.user_dashboard', [
            'streak' => $this->streak,
            'paginatedTasks' => $this->tasksQuery->paginate(8),
            'totalTasks' => $this->totalTasks,
            'completedTasks' => $this->completedTasks,
            'activeTasks' => $this->activeTasks,
            'highPriority' => $this->highPriority,
        ]);
    }

    // Create
    public $taskId = null;
    public $task = '';
    public $description = '';
    public $priority = 1;
    public $status = 1;
    public $start_date;
    public $end_date;

    protected $rules = [
        'task' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|integer|in:1,2,3',
        'status' => 'required|integer|in:1,2,3',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ];

    public function createTask()
    {
        $this->validate();

        Todo::create([
            'user_id' => Auth::id(),
            'task' => $this->task,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $this->resetTaskForm();

        $this->showTaskModal = false;

        session()->flash('success', 'Task created successfully.');
    }

    public function openCreateModal()
    {
        $this->resetTaskForm();

        $this->showTaskModal = true;
    }

    public function closeTaskModal()
    {
        $this->showTaskModal = false;
    }

    public function editTask($id)
    {
        $task = Todo::where('user_id', Auth::id())
            ->findOrFail($id);

        $this->taskId = $task->id;

        $this->task = $task->task;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->status = $task->status;

        $this->start_date = optional($task->start_date)->format('Y-m-d');
        $this->end_date = optional($task->end_date)->format('Y-m-d');

        $this->showTaskModal = true;
    }

    public function updateTask()
    {
        $this->validate();

        $task = Todo::where('user_id', Auth::id())
            ->findOrFail($this->taskId);

        $task->update([
            'task' => $this->task,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $this->resetTaskForm();

        $this->showTaskModal = false;

        session()->flash('success', 'Task updated successfully.');
    }

    public function openDetailsModal($id)
    {
        $this->selectedTask = Todo::where('user_id', Auth::id())
            ->findOrFail($id);

        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;

        $this->selectedTask = null;
    }

    public function deleteTask($id)
    {
        $task = Todo::where('user_id', Auth::id())
            ->findOrFail($id);

        $task->delete();

        session()->flash('success', 'Task deleted successfully.');
    }

    public function resetTaskForm()
    {
        $this->reset([
            'taskId',
            'task',
            'description',
            'priority',
            'status',
            'start_date',
            'end_date',
        ]);

        $this->priority = 1;
        $this->status = 1;
    }
}