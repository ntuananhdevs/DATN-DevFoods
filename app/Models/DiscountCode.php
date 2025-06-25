<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'min_requirement_type',
        'min_requirement_value',
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
        'applicable_ranks' => 'json',
        'valid_days_of_week' => 'json',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'valid_from_time' => 'datetime',
        'valid_to_time' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'rank_exclusive' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_requirement_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    /**
     * Define the possible values for applicable_items
     */
    const APPLICABLE_ITEMS_ALL = 'all_items';
    const APPLICABLE_ITEMS_ALL_PRODUCTS = 'all_products';
    const APPLICABLE_ITEMS_ALL_CATEGORIES = 'all_categories';
    const APPLICABLE_ITEMS_ALL_COMBOS = 'all_combos';
    const APPLICABLE_ITEMS_SPECIFIC_PRODUCTS = 'specific_products';
    const APPLICABLE_ITEMS_SPECIFIC_CATEGORIES = 'specific_categories';
    const APPLICABLE_ITEMS_SPECIFIC_COMBOS = 'specific_combos';
    const APPLICABLE_ITEMS_SPECIFIC_VARIANTS = 'specific_variants';

    public function promotionPrograms()
    {
        return $this->belongsToMany(PromotionProgram::class, 'promotion_discount_codes', 'discount_code_id', 'promotion_program_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'discount_code_branches');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all discount code products
     */
    public function products()
    {
        return $this->hasMany(DiscountCodeProduct::class);
    }

    /**
     * Get all specific product relations (product_id is not null)
     */
    public function specificProducts()
    {
        return $this->hasMany(DiscountCodeProduct::class)->whereNotNull('product_id');
    }

    /**
     * Get all specific category relations (category_id is not null)
     */
    public function specificCategories()
    {
        return $this->hasMany(DiscountCodeProduct::class)->whereNotNull('category_id');
    }

    /**
     * Get all specific combo relations (combo_id is not null)
     */
    public function specificCombos()
    {
        return $this->hasMany(DiscountCodeProduct::class)->whereNotNull('combo_id');
    }

    /**
     * Get all specific variant relations (product_variant_id is not null)
     */
    public function specificVariants()
    {
        return $this->hasMany(DiscountCodeProduct::class)->whereNotNull('product_variant_id');
    }

    public function users()
    {
        return $this->hasMany(UserDiscountCode::class);
    }

    public function usageHistory()
    {
        return $this->hasMany(DiscountUsageHistory::class);
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function isActiveNow()
    {
        $now = Carbon::now();
        return $this->is_active && 
               $this->start_date <= $now && 
               $this->end_date >= $now;
    }

    public function isExpired()
    {
        return $this->end_date < Carbon::now();
    }

    public function isUpcoming()
    {
        return $this->start_date > Carbon::now();
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        } elseif ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isUpcoming()) {
            return 'upcoming';
        } else {
            return 'active';
        }
    }

    /**
     * Determine if this discount code applies to all items
     */
    public function appliesToAllItems()
    {
        return $this->applicable_items === self::APPLICABLE_ITEMS_ALL;
    }

    /**
     * Determine if this discount code applies to specific products
     */
    public function appliesToSpecificProducts()
    {
        return $this->applicable_items === self::APPLICABLE_ITEMS_SPECIFIC_PRODUCTS;
    }

    /**
     * Determine if this discount code applies to specific categories
     */
    public function appliesToSpecificCategories()
    {
        return $this->applicable_items === self::APPLICABLE_ITEMS_SPECIFIC_CATEGORIES;
    }

    /**
     * Determine if this discount code applies to specific combos
     */
    public function appliesToSpecificCombos()
    {
        return $this->applicable_items === self::APPLICABLE_ITEMS_SPECIFIC_COMBOS;
    }

    /**
     * Determine if this discount code applies to specific variants
     */
    public function appliesToSpecificVariants()
    {
        return $this->applicable_items === self::APPLICABLE_ITEMS_SPECIFIC_VARIANTS;
    }

    /**
     * Get all combo IDs from the applied_ids field if applicable_items is combos_only or specific_combos
     * 
     * @return array
     */
    public function getAppliedComboIds()
    {
        if (in_array($this->applicable_items, ['combos_only', 'specific_combos'])) {
            return $this->specificCombos()->pluck('combo_id')->toArray();
        }
        return [];
    }
}
