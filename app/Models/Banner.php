<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title', 
        'image_path',
        'position',
        'description',
        'link',
        'is_active',
        'start_at',
        'end_at',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];
}