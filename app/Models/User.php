<?php

namespace App\Models;

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

    // Fields that can be mass-assigned
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'sex',
        'date_of_birth',
        'role',
    ];

    // Fields hidden in API responses
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Field casting
    protected $casts = [
        'password' => 'hashed',   // Laravel automatically hashes passwords
        'date_of_birth' => 'date',
    ];
}