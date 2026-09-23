<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'user_id',
        'product',
        'category',
        'price',
        'cost',
        'profit',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'stock' => 'integer',
    ];
}