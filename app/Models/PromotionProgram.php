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
}