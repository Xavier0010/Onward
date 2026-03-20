<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();

        $achievements = Achievement::all()->map(function($achievement) use ($user) {
            $unlocked = UserAchievement::where('user_id', $user->id)->where('achievement_id', $achievement->id)->exists();

            return [
                "id" => $achievement->id,
                "name" => $achievement->name,
                "description" => $achievement->description,
                "icon" => $achievement->icon,
                "unlocked" => $unlocked
            ];
        });

        return response()->json([
            "success" => true,
            "data" => $achievements
        ]);
    }
}
