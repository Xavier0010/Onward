<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAvatarBorder extends Model
{
    protected $fillable = [
        'user_id', 'avatar_border_id', 'active'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avatarBorder(): BelongsTo
    {
        return $this->belongsTo(AvatarBorder::class);
    }
}
