<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchStock extends Model
{
    /** @use HasFactory<\Database\Factories\BranchStockFactory> */
    use HasFactory;

    protected $table = 'branch_stocks';
    protected $fillable = [
        'branch_id',
        'product_variant_id',
        'stock_quantity',
    ];

    protected $casts = [
        'stock_quantity' => 'integer'
    ];

    public $timestamps = false;

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id', // Foreign key on product_variants table
            'id', // Foreign key on products table
            'product_variant_id', // Local key on branch_stocks table
            'product_id' // Local key on product_variants table
        );
    }
}
