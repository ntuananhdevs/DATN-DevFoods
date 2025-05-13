<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    use HasFactory;

    protected $table = 'product_variant_values';

    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
    ];

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}