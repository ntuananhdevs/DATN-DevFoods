<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    use HasFactory, Notifiable;
    

    protected $fillable = [
        'role_id',
        'user_name',
        'full_name',
        'email',
        'phone',
        'avatar',
        'google_id',
        'balance',
        'active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'balance' => 'decimal:2',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
