<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'image',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'applicable_scope',
        'applicable_items',
        'applicable_ranks',
        'rank_exclusive',
        'valid_days_of_week',
        'valid_from_time',
        'valid_to_time',
        'usage_type',
        'max_total_usage',
        'max_usage_per_user',
        'current_usage_count',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'display_order',
        'created_by',
    ];

    protected $casts = [
        'applicable_ranks' => 'array',
        'valid_days_of_week' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'rank_exclusive' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    public function promotionPrograms()
    {
        return $this->belongsToMany(PromotionProgram::class, 'promotion_discount_codes', 'discount_code_id', 'promotion_program_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'discount_code_branches', 'discount_code_id', 'branch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}