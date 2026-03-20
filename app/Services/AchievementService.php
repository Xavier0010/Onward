<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\Todo;
use App\Models\User;

class AchievementService {
    public function check($user, $type) {
        switch ($type) {
            case 'tasks_created':
                $currentValue = Todo::where('user_id', $user->id)->count();
                break;
            
            case 'tasks_completed':
                $currentValue = Todo::where('user_id', $user->id)->where('status', 3)->count();
                break;

            case 'streak_count':
                $currentValue = $user->streak_count;
                break;

            case 'register':
                $currentValue = 1;
                break;

            default:
                return;
        };
        
        $achievements = Achievement::where('type', $type)->get();

        foreach ($achievements as $achievement) {
            if ($currentValue >= $achievement->target_value) {
                $exists = UserAchievement::where('user_id', $user->id)->where('achievement_id', $achievement->id)->exists();

                if (!$exists) {
                    UserAchievement::create([
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                        'unlocked_at' => now()
                    ]);
                }
            }
        }
    }

}