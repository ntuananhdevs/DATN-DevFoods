<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class);
    }

    // Add this method to create the relationship with ProductVariant
    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values', 'attribute_value_id', 'product_variant_id');
    }
}