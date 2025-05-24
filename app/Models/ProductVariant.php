<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'active'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantValues(): BelongsToMany
    {
        return $this->belongsToMany(VariantValue::class, 'product_variant_details')
            ->withTimestamps();
    }

    public function branchStocks()
    {
        return $this->hasMany(BranchStock::class);
    }
}