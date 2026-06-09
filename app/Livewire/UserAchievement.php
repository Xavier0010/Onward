<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\User\AchievementController;

class UserAchievement extends Component
{
    public $first_name = "";
    public $username = "";
    public $categories = [];
    public $achievements = [];
    public $unlockedAchievements = [];
    public $totalAchievements = 0;
    public $unlockedCount = 0;
    public $completionPercentage = 0;

    public function mount()
    {
        if(!Auth::check()){
            return redirect('/login');
        }

        $achievementRequest = Request::create('/api/user/achievement', 'GET');
        $achievementRequest->setUserResolver(function () {
            return Auth::user();
        });
        
        $achievementController = new AchievementController();
        $achievementResponse = $achievementController->index($achievementRequest);
        $this->achievements = json_decode($achievementResponse->getContent(), true)['data'];

        $this->first_name = Auth::user()->first_name;
        $this->username = Auth::user()->username;
        $this->categories = [];
        $this->totalAchievements = count($this->achievements);

        // Group achievements into categories
        $grouped = collect($this->achievements)->groupBy(function ($achievement) {
            return ucfirst(str_replace('_', ' ', $achievement['type'] ?? 'General'));
        });

        $this->categories = $grouped->map(function ($badges, $label) {
            $processedBadges = $badges->map(function ($badge) {
                $unlocked = $badge['unlocked'] ?? false;

                return [
                    'id' => $badge['id'] ?? null,
                    'name' => $badge['name'] ?? '',
                    'description' => $badge['description'] ?? '',
                    'icon' => $badge['icon'] ?? '',
                    'unlocked' => $unlocked,
                    'earned_at' => $badge['earned_at'] ?? null
                ];
            })->toArray();

            return [
                'label' => $label,
                'total' => count($processedBadges),
                'unlockedCount' => collect($processedBadges)->where('unlocked', true)->count(),
                'badges' => $processedBadges
            ];
        })->values()->toArray();

        // Get unlocked achievements
        $this->unlockedAchievements = collect($this->achievements)->filter(function ($achievement) {
            return $achievement['unlocked'];
        });

        $this->unlockedCount = $this->unlockedAchievements->count();
        $this->completionPercentage = $this->totalAchievements > 0 ? round(($this->unlockedCount / $this->totalAchievements) * 100) : 0;
    }

    public function render()
    {
        return view('livewire.user_achievement');
    }
}
