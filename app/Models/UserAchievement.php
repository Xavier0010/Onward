<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAchievement extends Model
{
    use HasFactory;

    protected $table = 'user_achievements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'unlocked_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function achievement() {
        return $this->belongsTo(Achievement::class);
    }
}
