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

    public $timestamps = false;

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
