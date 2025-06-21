<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'combo_id',
        'product_variant_id',
        'quantity'
    ];

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_variant_id', 'id')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.id', $this->product_variant_id);
    }
}