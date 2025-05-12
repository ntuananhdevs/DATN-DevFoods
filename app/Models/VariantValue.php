<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_attribute_id',
        'product_variant_id',
        'value',
    ];

    /**
     * Lấy thuộc tính biến thể
     */
    public function variantAttribute()
    {
        return $this->belongsTo(VariantAttribute::class);
    }

    /**
     * Lấy biến thể sản phẩm
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}