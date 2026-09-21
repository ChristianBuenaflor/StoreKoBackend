<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'user_name',
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