<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'name',
        'address',
        'phone',
        'email',
        'manager_user_id',
        'latitude',
        'longitude',
        'opening_hour',
        'closing_hour',
        'active',
        'balance',
        'rating',
        'reliability_score',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'active' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'reliability_score' => 'integer',
        'opening_hour' => 'datetime:H:i',
        'closing_hour' => 'datetime:H:i',
    ];

    /**
     * Quan hệ với User (Manager)
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    /**
     * Quan hệ với BranchImage
     */
    public function images(): HasMany
    {
        return $this->hasMany(BranchImage::class);
    }

    /**
     * Lấy ảnh chính của chi nhánh
     */
    public function primaryImage()
    {
        return $this->hasOne(BranchImage::class)->where('is_primary', true);
    }

    /**
     * Scope để lọc chi nhánh đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope để tìm kiếm theo tên hoặc mã chi nhánh
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('branch_code', 'like', "%{$search}%");
    }

    /**
     * Accessor để format số điện thoại
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone;
    }

    /**
     * Accessor để kiểm tra chi nhánh có đang mở không
     */
    public function getIsOpenAttribute()
    {
        $now = now()->format('H:i');
        return $now >= $this->opening_hour && $now <= $this->closing_hour && $this->active;
    }

    /**
     * Mutator để format branch_code
     */
    public function setBranchCodeAttribute($value)
    {
        $this->attributes['branch_code'] = strtoupper($value);
    }
}