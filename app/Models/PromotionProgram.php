<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProgram extends Model
{
    protected $table = 'promotion_programs';

    protected $fillable = [
        'name', 'description', 'banner_image', 'thumbnail_image', 'applicable_scope',
        'start_date', 'end_date', 'is_active', 'is_featured', 'display_order', 'created_by'
    ];

    protected $casts = [
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

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'promotion_discount_codes');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'promotion_branches');
    }
}