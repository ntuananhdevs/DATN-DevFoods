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
    use HasFactory, Notifiable, SoftDeletes;


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
        'birthday',
        'gender',
        'google_id',
        'remember_token',
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
            'birthday' => 'date',
            'active' => 'boolean',
            'balance' => 'decimal:2',
            'rank_updated_at' => 'datetime',
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

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
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

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class)->with('product');
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

    public function userDiscountCodes()
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

    public function rewardPointHistories()
    {
        return $this->hasMany(RewardPointHistory::class, 'user_id');
    }

    public function discountUsageHistory()
    {
        return $this->hasMany(DiscountUsageHistory::class);
    }
    
    /**
     * Lấy các đánh giá tài xế mà người dùng đã thực hiện
     */
    public function driverRatings()
    {
        return $this->hasMany(DriverRating::class);
    }

    /**
     * Check if user is authenticated via Google
     */
    public function isGoogleUser()
    {
        return !empty($this->google_id);
    }

    /**
     * Check if user is social login user
     */
    public function isSocialUser()
    {
        return $this->isGoogleUser();
    }

    /**
     * Get the user's name attribute (alias for full_name)
     * Access via $user->name
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    /**
     * Get full avatar URL from filename
     * Access via $user->avatar_url
     */
    public function getAvatarUrlAttribute()
    {
        if (empty($this->attributes['avatar'])) {
            return null;
        }

        $avatar = $this->attributes['avatar'];

        // If it's already a full URL, return as is
        if (str_starts_with($avatar, 'http')) {
            return $avatar;
        }

        // Build S3 URL from filename
        $bucket = env('AWS_BUCKET');
        $region = env('AWS_DEFAULT_REGION', 'us-east-1');

        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/users/avatars/{$avatar}";
        } else {
            return "https://{$bucket}.s3.{$region}.amazonaws.com/users/avatars/{$avatar}";
        }
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'manager_user_id', 'id');
    }
}
