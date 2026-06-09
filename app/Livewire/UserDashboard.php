<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\User\UserDashboardController;
use App\Http\Controllers\Api\User\UserTodoController;
use App\Http\Controllers\Api\User\FriendController;

class UserDashboard extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public $searchQuery = '';
    public $showFilterModal = false;

    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public $filterStatuses = [];
    public $filterPriorities = [];
    public $filterDateFrom = null;
    public $filterDateTo = null;

    public $showTaskModal = false;
    public $showDetailsModal = false;
    public $showConfirmModal = false;

    public $confirmAction = '';
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $confirmParam = null;

    public $selectedTask = null;

    public $quote;
    public $totalXp = 0;
    public $weeklyXp = 0;
    public $availableXp = 0;

    // Dashboard stats (loaded from API)
    public $totalTasks = 0;
    public $completedTasks = 0;
    public $activeTasks = 0;
    public $highPriority = 0;
    public $streak = 0;

    public function mount()
    {
        $this->refreshQuote();
        $this->loadDashboardData();
    }

    /**
     * Helper: call an API controller method following existing pattern
     */
    private function apiRequest($controllerClass, $method, $httpMethod = 'GET', $data = [], $routeParams = [])
    {
        $files = [];
        if (!empty($data['_files'])) {
            foreach ($data['_files'] as $key => $fileObj) {
                if ($fileObj) {
                    $files[$key] = $fileObj;
                }
            }
            unset($data['_files']);
        }

        $request = Request::create('/api/placeholder', $httpMethod, $data, [], $files);
        $request->setUserResolver(fn() => Auth::user());
        
        $controller = new $controllerClass();

        if (!empty($routeParams)) {
            $response = $controller->$method($request, ...$routeParams);
        } else {
            $response = $controller->$method($request);
        }
        
        return json_decode($response->getContent(), true);
    }

    /**
     * Load dashboard stats from API
     */
    private function loadDashboardData()
    {
        $result = $this->apiRequest(UserDashboardController::class, 'index');
        
        if ($result['success'] ?? false) {
            $data = $result['data'];
            $this->streak = $data['current_streak'];
            $this->totalTasks = $data['total_tasks'];
            $this->completedTasks = $data['completed_tasks'];
            $this->activeTasks = $data['active_tasks'];
            $this->highPriority = $data['high_priority'];
            $this->totalXp = $data['total_xp'];
            $this->weeklyXp = $data['weekly_xp'];
            $this->availableXp = $data['available_xp'];
        }
    }

    public function refreshQuote()
    {
        $quotes = config('quotes');
        $this->quote = $quotes[array_rand($quotes)];
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->filterStatuses = [];
        $this->filterPriorities = [];
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->showFilterModal = false;
        $this->resetPage();
    }

    /**
     * Main task query — still using Eloquent for pagination support
     * (API returns full list; pagination is more practical with Query Builder)
     */
    public function getTasksQueryProperty()
    {
        return \App\Models\Todo::query()
            ->where('user_id', Auth::id())
            ->when($this->searchQuery, function ($query) {
                $query->where('task', 'like', '%' . $this->searchQuery . '%');
            })
            ->when(!empty($this->filterPriorities), function ($query) {
                $query->whereIn('priority', $this->filterPriorities);
            })
            ->when(!empty($this->filterStatuses), function ($query) {
                $query->whereIn('status', $this->filterStatuses);
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('end_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('end_date', '<=', $this->filterDateTo);
            })
            ->orderBy($this->sortBy, $this->sortDir);
    }

    /**
     * Load leaderboard from friend API
     */
    public function getLeaderboardProperty()
    {
        $result = $this->apiRequest(FriendController::class, 'leaderboard', 'GET', ['type' => 'xp']);
        
        if ($result['success'] ?? false) {
            $data = $result['data'];
            $allEntries = collect($data)->map(function ($entry, $index) {
                $medal = null;
                $rank = $entry['rank'];
                if ($rank === 1) $medal = 'gold';
                elseif ($rank === 2) $medal = 'silver';
                elseif ($rank === 3) $medal = 'bronze';

                $colors = ['#22c55e', '#6366f1', '#f59e0b', '#ec4899', '#f97316', '#3b82f6', '#8b5cf6'];
                $initials = strtoupper(substr($entry['first_name'], 0, 1) . substr($entry['last_name'], 0, 1));

                return [
                    'rank' => $rank,
                    'name' => $entry['is_current_user'] ? 'You' : $entry['full_name'],
                    'xp' => $entry['value'],
                    'medal' => $medal,
                    'avatar' => $initials,
                    'color' => $colors[$index % count($colors)],
                ];
            });
            return $allEntries->toArray();
        }
        
        return [];
    }

    /**
     * Toggle complete status via API
     */
    public function toggleTask($taskId)
    {
        $this->apiRequest(UserTodoController::class, 'toggle', 'PUT', [], [$taskId]);
        $this->loadDashboardData();
    }

    public function rollPriority($taskId)
    {
        $task = \App\Models\Todo::where('user_id', Auth::id())->findOrFail($taskId);
        if ($task->priority == 3) {
            $task->priority = 1;
        } elseif ($task->priority == 1) {
            $task->priority = 2;
        } else {
            $task->priority = 3;
        }
        $task->save();
        $this->loadDashboardData();
    }

    public function rollStatus($taskId)
    {
        $task = \App\Models\Todo::where('user_id', Auth::id())->findOrFail($taskId);
        if ($task->status == 1) {
            $task->status = 2;
        } else {
            if ($task->status == 3) {
                $task->completed_at = null;
            }
            $task->status = 1;
        }
        $task->save();
        $this->loadDashboardData();
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
            'leaderboard' => $this->leaderboard,
        ]);
    }

    // ── Create / Edit Task ──

    public $taskId = null;
    public $task = '';
    public $description = '';
    public $priority = 1;
    public $status = 1;
    public $start_date;
    public $end_date;
    public $file;

    protected $rules = [
        'task' => 'required|string|max:255',
        'description' => 'nullable|string',
        'priority' => 'required|integer|in:1,2,3',
        'status' => 'required|integer|in:1,2,3',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'file' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,xlsx,zip,txt,md,csv,doc,docx,ppt,pptx,pdf', // 10MB
    ];

    public function createTask()
    {
        $this->validate();

        $this->apiRequest(UserTodoController::class, 'store', 'POST', [
            'task' => $this->task,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            '_files' => ['file' => $this->file],
        ]);

        $this->resetTaskForm();
        $this->showTaskModal = false;
        $this->loadDashboardData();

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
        $task = \App\Models\Todo::where('user_id', Auth::id())
            ->findOrFail($id);

        $this->taskId = $task->id;

        $this->task = $task->task;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->status = $task->status;

        $this->start_date = optional($task->start_date)->format('Y-m-d');
        $this->end_date = optional($task->end_date)->format('Y-m-d');
        $this->file = null;

        $this->showTaskModal = true;
    }

    public function updateTask()
    {
        $this->validate();

        $this->apiRequest(UserTodoController::class, 'update', 'PUT', [
            'task' => $this->task,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            '_files' => ['file' => $this->file],
        ], [$this->taskId]);

        $this->resetTaskForm();
        $this->showTaskModal = false;
        $this->loadDashboardData();

        session()->flash('success', 'Task updated successfully.');
    }

    public function openDetailsModal($id)
    {
        $this->selectedTask = \App\Models\Todo::where('user_id', Auth::id())
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
        $this->apiRequest(UserTodoController::class, 'destroy', 'DELETE', [], [$id]);
        $this->loadDashboardData();

        session()->flash('success', 'Task deleted successfully.');
    }

    public function openConfirmModal($action, $title, $message, $param = null)
    {
        $this->confirmAction = $action;
        $this->confirmTitle = $title;
        $this->confirmMessage = $message;
        $this->confirmParam = $param;
        $this->showConfirmModal = true;
    }

    public function executeConfirmAction()
    {
        if ($this->confirmAction === 'deleteTask' && $this->confirmParam) {
            $this->deleteTask($this->confirmParam);
        }
        $this->showConfirmModal = false;
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
            'file',
        ]);

        $this->priority = 1;
        $this->status = 1;
    }
}