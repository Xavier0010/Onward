<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use App\Models\Todo;
use App\Models\Friendship;
use App\Models\ActivityEvent;
use App\Models\ProfileAchievement;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * GET /api/user/profile — Own profile (enriched)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => $this->buildProfileData($user, $user),
        ]);
    }

    /**
     * GET /api/user/profile/{username} — View any user's profile
     */
    public function show(Request $request, $id)
    {
        $currentUser = $request->user();
        $profileUser = User::findOrFail($id);

        $data = $this->buildProfileData($profileUser, $currentUser);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Build enriched profile data for a user.
     */
    private function buildProfileData(User $profileUser, User $currentUser)
    {
        $tasksCompleted = Todo::where('user_id', $profileUser->id)->where('status', 3)->count();

        // Weekly XP
        $weekStart = now()->startOfWeek();
        $weeklyXp = Todo::where('user_id', $profileUser->id)
            ->where('status', 3)
            ->where('completed_at', '>=', $weekStart)
            ->get()
            ->sum(function ($todo) {
                return match ((int) $todo->priority) {
                    1 => 10, 2 => 15, 3 => 20, default => 10,
                };
            });
        $streakXp = 0;
        if ($profileUser->streak_count >= 30) $streakXp += 200;
        if ($profileUser->streak_count >= 7) $streakXp += 50;
        $weeklyXp += $streakXp;

        // Friend count & rank
        $friendIds = $profileUser->getFriendIds();
        $friendCount = count($friendIds);

        // Friend rank (among all users by XP)
        $allUsers = User::where('role', 'user')->get();
        $xpRanking = $allUsers->map(function ($u) use ($weekStart) {
            $xp = Todo::where('user_id', $u->id)->where('status', 3)
                ->where('completed_at', '>=', $weekStart)->get()
                ->sum(fn($t) => match((int) $t->priority) { 1 => 10, 2 => 15, 3 => 20, default => 10 });
            if ($u->streak_count >= 30) $xp += 200;
            if ($u->streak_count >= 7) $xp += 50;
            return ['id' => $u->id, 'xp' => $xp];
        })->sortByDesc('xp')->values();
        $friendRank = $xpRanking->search(fn($item) => $item['id'] === $profileUser->id) + 1;

        // Recent activity
        $recentActivity = ActivityEvent::where('user_id', $profileUser->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($e) {
                return [
                    'type' => $e->type,
                    'data' => $e->data,
                    'created_at' => $e->created_at->diffForHumans(),
                ];
            });

        // Showcase achievements
        $showcaseAchievements = ProfileAchievement::with('achievement')
            ->where('user_id', $profileUser->id)
            ->orderBy('display_order')
            ->limit(8)
            ->get()
            ->map(function ($pa) {
                return [
                    'id' => $pa->achievement->id,
                    'name' => $pa->achievement->name,
                    'description' => $pa->achievement->description,
                    'icon' => $pa->achievement->icon,
                    'display_order' => $pa->display_order,
                ];
            });

        // Active avatar border
        $activeAvatarBorder = null;
        $activeUserBorder = \App\Models\UserAvatarBorder::where('user_id', $profileUser->id)->where('active', true)->first();
        if ($activeUserBorder) {
            $border = \App\Models\AvatarBorder::find($activeUserBorder->avatar_border_id);
            if ($border) {
                $activeAvatarBorder = [
                    'id' => $border->id,
                    'name' => $border->name,
                    'color' => $border->color,
                ];
            }
        }

        // Friendship status (for other user's profile)
        $friendshipStatus = null;
        $friendshipId = null;
        if ($currentUser->id !== $profileUser->id) {
            $friendship = Friendship::where(function ($q) use ($currentUser, $profileUser) {
                $q->where('sender_id', $currentUser->id)->where('receiver_id', $profileUser->id);
            })->orWhere(function ($q) use ($currentUser, $profileUser) {
                $q->where('sender_id', $profileUser->id)->where('receiver_id', $currentUser->id);
            })->first();

            if ($friendship) {
                $friendshipStatus = $friendship->status;
                $friendshipId = $friendship->id;
            }
        }

        return [
            'id' => $profileUser->id,
            'username' => $profileUser->username,
            'email' => $profileUser->email,
            'first_name' => $profileUser->first_name,
            'last_name' => $profileUser->last_name,
            'full_name' => $profileUser->first_name . ' ' . $profileUser->last_name,
            'gender' => $profileUser->gender,
            'date_of_birth' => $profileUser->date_of_birth,
            'avatar' => $profileUser->avatar,
            'avatar_url' => $profileUser->avatar ? Storage::url($profileUser->avatar) : null,
            'nationality' => $profileUser->nationality,
            'streak_count' => $profileUser->streak_count,
            'best_streak' => $profileUser->best_streak,
            'tasks_completed' => $tasksCompleted,
            'weekly_xp' => $weeklyXp,
            'friend_count' => $friendCount,
            'friend_rank' => $friendRank,
            'recent_activity' => $recentActivity,
            'showcase_achievements' => $showcaseAchievements,
            'active_avatar_border' => $activeAvatarBorder,
            'is_own_profile' => $currentUser->id === $profileUser->id,
            'friendship_status' => $friendshipStatus,
            'friendship_id' => $friendshipId,
        ];
    }

    /**
     * PUT /api/user/profile — Update profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            "username" => "sometimes|min:6|unique:users,username," . $user->id,
            "first_name" => "sometimes|string",
            "last_name" => "sometimes|string",
            "gender" => "sometimes|in:male,female",
            "date_of_birth" => "sometimes|date",
            "nationality" => "nullable|string|max:100",
            "old_password" => "required_with:new_password",
            "new_password" => "nullable|min:8|confirmed"
        ]);

        $user->fill([
            "username" => $validated['username'] ?? $user->username,
            "first_name" => $validated['first_name'] ?? $user->first_name,
            "last_name" => $validated['last_name'] ?? $user->last_name,
            "gender" => $validated['gender'] ?? $user->gender,
            "date_of_birth" => $validated['date_of_birth'] ?? $user->date_of_birth,
            "nationality" => array_key_exists('nationality', $validated) ? $validated['nationality'] : $user->nationality,
        ]);

        if (!empty($validated['new_password'])) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    "success" => false,
                    "message" => "Old password incorrect!"
                ], 401);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Profile updated!",
            "data" => $user
        ]);
    }

    /**
     * POST /api/user/avatar — Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated!',
            'data' => [
                'avatar' => $path,
                'avatar_url' => Storage::url($path),
            ],
        ]);
    }

    /**
     * PUT /api/user/profile/achievements — Update showcase achievements
     */
    public function updateShowcaseAchievements(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'achievement_ids' => 'required|array|max:8',
            'achievement_ids.*' => 'integer|exists:achievements,id',
        ]);

        // Verify all are unlocked by user
        $unlockedIds = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')->toArray();

        $requestedIds = $request->achievement_ids;
        foreach ($requestedIds as $id) {
            if (!in_array($id, $unlockedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only showcase unlocked achievements.',
                ], 422);
            }
        }

        // Clear existing and re-insert
        ProfileAchievement::where('user_id', $user->id)->delete();

        foreach ($requestedIds as $index => $achievementId) {
            ProfileAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievementId,
                'display_order' => $index,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Showcase achievements updated!',
        ]);
    }

    /**
     * GET /api/user/activity — Own recent activity
     */
    public function getActivity(Request $request)
    {
        $user = $request->user();

        $events = ActivityEvent::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($e) {
                return [
                    'type' => $e->type,
                    'data' => $e->data,
                    'created_at' => $e->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * DELETE /api/user/profile — Delete account
     */
    public function destroy(Request $request)
    {
        $request->user()->delete();

        return response()->json([
            'success' => true,
            'message' => "Account deleted successfully!"
        ]);
    }
}
