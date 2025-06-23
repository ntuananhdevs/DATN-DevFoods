<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'image',
        'description',
        'price',
        'active'
    ];

    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'combo_items', 'combo_id', 'product_variant_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_items', 'combo_id', 'product_variant_id')
            ->join('product_variants', 'product_variants.id', '=', 'combo_items.product_variant_id')
            ->select('products.*')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}