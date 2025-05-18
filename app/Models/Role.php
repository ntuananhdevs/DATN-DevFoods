<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array', // Cast quyền thành mảng
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }
    
}
