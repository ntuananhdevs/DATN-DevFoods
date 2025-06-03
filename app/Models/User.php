<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\Admin\UserFactory> */
    use HasFactory, Notifiable , SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'full_name',
        'email',
        'phone',
        'avatar',
        'google_id',
        'balance',
        'user_rank_id',
        'total_spending',
        'total_orders',
        'rank_updated_at',
        'active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    // Trong phương thức role()
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
       public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withTimestamps();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // app/Models/User.php
    public function wishlist()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function userRank()
    {
        return $this->belongsTo(UserRank::class, 'user_rank_id');
    }

    public function userRankHistory()
    {
        return $this->hasMany(UserRankHistory::class);
    }

    public function images()
    {
        return $this->hasMany(UserImage::class);
    }

    public function primaryImage()
    {
        return $this->hasMany(UserImage::class)->where('is_primary', true);
    }

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'user_discount_codes')
                    ->withPivot('usage_count', 'status', 'assigned_at', 'first_used_at', 'last_used_at');
    }

    public function createdDiscountCodes()
    {
        return $this->hasMany(DiscountCode::class, 'created_by');
    }

    public function createdPromotionPrograms()
    {
        return $this->hasMany(PromotionProgram::class, 'created_by');
    }

    public function discountUsageHistory()
    {
        return $this->hasMany(DiscountUsageHistory::class);
    }
}
