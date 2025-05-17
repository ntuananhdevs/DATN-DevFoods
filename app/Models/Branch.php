<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_name',
        'latitude',
        'longitude',
        'opening_hour',
        'closing_hour',
        'active',
        'balance',
        'rating',
        'reliability_score'
    ];

    protected $casts = [
        'opening_hour' => 'datetime:H:i',
        'closing_hour' => 'datetime:H:i',
        'active' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}