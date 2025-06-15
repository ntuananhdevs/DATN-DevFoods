<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeProductVariant extends Model
{
    use HasFactory;
    
    protected $table = 'discount_code_product_variants';
    protected $fillable = ['discount_code_id', 'product_variant_id'];

    // Relationships
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
} 