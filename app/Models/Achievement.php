<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'achievements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'type', // register, streak, tasks_created, tasks_completed
        'target_value'
    ];

    public function users() {
        return $this->belongsToMany(
            User::class,
            'user_achievements',
            'achievement_id',
            'user_id'
        )->withPivot('unlocked_at');
    }
}
