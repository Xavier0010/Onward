<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileAchievement extends Model
{
    protected $table = 'profile_achievements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'display_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}
