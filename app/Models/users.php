<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    use HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'store_name',
        'name',
        'email',
        'phone_number',
        'password',
        'is_archived',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}