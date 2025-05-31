<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $table = 'discount_codes';
    protected $fillable = [
        'code', 'name', 'description', 'image', 'discount_type', 'discount_value', 'min_order_amount',
        'max_discount_amount', 'applicable_scope', 'applicable_items', 'applicable_ranks', 'rank_exclusive',
        'valid_days_of_week', 'valid_from_time', 'valid_to_time', 'usage_type', 'max_total_usage',
        'max_usage_per_user', 'current_usage_count', 'start_date', 'end_date', 'is_active', 'is_featured',
        'display_order', 'created_by'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'applicable_ranks' => 'array',
        'valid_days_of_week' => 'array',
        'valid_from_time' => 'datetime:H:i',
        'valid_to_time' => 'datetime:H:i',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean'
    ];

    // Mối quan hệ
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_discount_codes')
                    ->withPivot('usage_count', 'status', 'assigned_at', 'first_used_at', 'last_used_at');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'discount_code_branches');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_code_products');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'discount_code_products');
    }

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'discount_code_products');
    }

    public function promotionPrograms()
    {
        return $this->belongsToMany(PromotionProgram::class, 'promotion_discount_codes');
    }

    public function usageHistory()
    {
        return $this->hasMany(DiscountUsageHistory::class);
    }
}
