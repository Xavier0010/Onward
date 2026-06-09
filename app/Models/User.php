<?php

namespace App\Models;

use Dom\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'role',
        'avatar',
        'nationality',
        'best_streak',
        'spent_xp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    public function todos() {
        return $this->hasMany(Todo::class, 'user_id');
    }

    public function achievements() {
        return $this->belongsToMany(
            Achievement::class,
            'user_achievements',
            'user_id',
            'achievement_id'
        )->withPivot('unlocked_at');
    }

    // ── Friendship relationships ──

    public function sentFriendRequests() {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests() {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    /**
     * Get IDs of all accepted friends (both directions).
     */
    public function getFriendIds(): array
    {
        $sent = Friendship::where('sender_id', $this->id)
            ->where('status', 'accepted')->pluck('receiver_id');
        $received = Friendship::where('receiver_id', $this->id)
            ->where('status', 'accepted')->pluck('sender_id');
        return $sent->merge($received)->unique()->values()->toArray();
    }

    /**
     * Query builder for accepted friends.
     */
    public function friends()
    {
        $friendIds = $this->getFriendIds();
        return User::whereIn('id', $friendIds);
    }

    // ── Notifications & Activity ──

    public function appNotifications() {
        return $this->hasMany(AppNotification::class);
    }

    public function activityEvents() {
        return $this->hasMany(ActivityEvent::class);
    }

    public function profileAchievements() {
        return $this->hasMany(ProfileAchievement::class);
    }

    public function userAvatarBorders() {
        return $this->hasMany(UserAvatarBorder::class);
    }

    public function ownedAvatarBorders() {
        return $this->belongsToMany(AvatarBorder::class, 'user_avatar_borders');
    }

    public function getTotalXpAttribute() {
        $taskXp = 0;
        $todos = $this->todos()->where('status', 3)->get();
        
        foreach ($todos as $todo) {
            if (property_exists($todo, 'priority')) {
                switch ((int) $todo->priority) {
                    case 1: $taskXp += 10; break;
                    case 2: $taskXp += 15; break;
                    case 3: $taskXp += 20; break;
                    default: $taskXp +=10; break;
                }
            }
        }
        
        $streakXp = 0;
        if (property_exists($this, 'streak_count')) {
            if ($this->streak_count >=30) $streakXp +=200;
            if ($this->streak_count >=7) $streakXp +=50;
        }
        
        return $taskXp + $streakXp;
    }
    
    public function getAvailableXpAttribute() {
        $total = $this->total_xp;
        $spent = property_exists($this, 'spent_xp') ? $this->spent_xp : 0;
        return $total - $spent;
    }
}