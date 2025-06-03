<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantDetail extends Model
{
    use HasFactory;

    protected $table = 'product_variant_details';
    protected $fillable = ['product_variant_id', 'variant_value_id'];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function variantValue()
    {
        return $this->belongsTo(VariantValue::class, 'variant_value_id');
    }
}