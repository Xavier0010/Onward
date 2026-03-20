<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $table = "todos";
    protected $primaryKey = 'id';

    public const STATUS_PENDING = 1;
    public const STATUS_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;

    protected $fillable = [
        'user_id',
        'task',
        'description',
        'start_date',
        'end_date',
        'completed_at',
        'priority', // 1 low, 2 medium, 3 high
        'status',  // 1 pending, 2 on progress, 3 completed
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
