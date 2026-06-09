<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Todo;
use App\Models\UserAchievement;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public $totalUsers;
    public $totalTasksCreated;
    public $totalTasksCompleted;
    public $activeUsers;
    
    public $highestStreak;
    public $averageStreak;
    
    public $userGrowth = [];
    public $priorityChart = [];
    public $activityHeatmap = [];
    
    public $mostUnlocked;
    public $rarest;
    public $averageAchievements;

    public function mount()
    {
        $this->loadStats();
    }
    
    private function loadStats()
    {
        $this->totalUsers = User::where('role', 'user')->count();
        $this->totalTasksCreated = Todo::count();
        $this->totalTasksCompleted = Todo::where('status', 3)->count();
        
        $this->activeUsers = User::where('last_login_date', '>=', now()->subDays(7)->toDateString())->count();
        
        $this->highestStreak = (User::max('best_streak') ?? User::max('streak_count')) ?? 0;
        $this->averageStreak = round(User::where('role', 'user')->avg('streak_count') ?? 0, 1);
        
        // Growth (last 7 days)
        $growth = User::where('role', 'user')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();
            
        $this->userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $this->userGrowth[$date] = $growth[$date] ?? 0;
        }
            
        // Priority
        $this->priorityChart = [
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

        $this->activityHeatmap = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $this->activityHeatmap[$date] = $activity[$date] ?? 0;
        }
        
        $this->mostUnlocked = Achievement::withCount('users')->orderByDesc('users_count')->first();
        $this->rarest = Achievement::withCount('users')->orderBy('users_count')->first();
        $this->averageAchievements = $this->totalUsers > 0 ? round(UserAchievement::count() / $this->totalUsers, 1) : 0;
    }
    
    public function render()
    {
        return view('livewire.admin_dashboard', [
            'mostUnlocked' => $this->mostUnlocked,
            'rarest' => $this->rarest,
            'averageAchievements' => $this->averageAchievements
        ]);
    }
}