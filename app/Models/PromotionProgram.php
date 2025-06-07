<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'banner_image',
        'thumbnail_image',
        'applicable_scope',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'display_order',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'applicable_scope' => 'string',
    ];

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'promotion_discount_codes', 'promotion_program_id', 'discount_code_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'promotion_branches', 'promotion_program_id', 'branch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    // Accessor cho tổng số lượt sử dụng
    public function getTotalUsageCountAttribute()
    {
        return $this->discountCodes->sum('current_usage_count');
    }

    // Accessor cho tổng giới hạn sử dụng
    public function getTotalUsageLimitAttribute()
    {
        $total = $this->discountCodes->sum('max_total_usage');
        return $total > 0 ? $total : null;
    }

    // Accessor cho value_range (từ giải pháp trước)
    public function getValueRangeAttribute()
    {
        $discountCodes = $this->discountCodes;
        $percentageValues = $discountCodes->where('discount_type', 'percentage')
            ->pluck('discount_value')
            ->filter()
            ->values();
        $fixedAmountValues = $discountCodes->where('discount_type', 'fixed_amount')
            ->pluck('discount_value')
            ->filter()
            ->values();
        $hasFreeShipping = $discountCodes->where('discount_type', 'free_shipping')->isNotEmpty();

        $value_range = [];
        if ($percentageValues->isNotEmpty()) {
            $minPercentage = $percentageValues->min();
            $maxPercentage = $percentageValues->max();
            $value_range[] = $minPercentage == $maxPercentage
                ? "{$minPercentage}%"
                : "{$minPercentage}% - {$maxPercentage}%";
        }
        if ($fixedAmountValues->isNotEmpty()) {
            $minAmount = $fixedAmountValues->min();
            $maxAmount = $fixedAmountValues->max();
            $value_range[] = $minAmount == $maxAmount
                ? number_format($minAmount) . ' đ'
                : number_format($minAmount) . ' đ - ' . number_format($maxAmount) . ' đ';
        }
        if ($hasFreeShipping) {
            $value_range[] = 'Miễn phí vận chuyển';
        }

        return !empty($value_range) ? implode(', ', $value_range) : 'Chưa xác định';
    }

    // Accessor cho program_type
    public function getProgramTypeAttribute()
    {
        $discountTypes = $this->discountCodes->pluck('discount_type')->unique()->toArray();
        if (count($discountTypes) == 1) {
            switch ($discountTypes[0]) {
                case 'percentage':
                    return ['class' => 'discount', 'text' => 'Giảm giá %'];
                case 'fixed_amount':
                    return ['class' => 'discount', 'text' => 'Giảm giá cố định'];
                case 'free_shipping':
                    return ['class' => 'special', 'text' => 'Miễn phí vận chuyển'];
            }
        }
        return ['class' => 'special', 'text' => 'Kết hợp'];
    }
}