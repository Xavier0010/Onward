<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvatarBorder extends Model
{
    protected $fillable = [
        'name', 'rarity', 'price', 'color'
    ];

    public function userAvatarBorders(): HasMany
    {
        return $this->hasMany(UserAvatarBorder::class);
    }
}
