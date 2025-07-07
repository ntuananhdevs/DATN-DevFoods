<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $appends = ['price', 'variant_description'];

    /**
     * Get the price for the variant.
     *
     * @return float
     */
    public function getPriceAttribute()
    {
        // Get the base price from the product
        $basePrice = $this->product ? $this->product->base_price : 0;
        
        // Add any price adjustments from variant values
        $priceAdjustments = $this->variantValues->sum('price_adjustment');
        
        return floatval($basePrice) + floatval($priceAdjustments);
    }

    /**
     * Get a description of the variant based on its values.
     *
     * @return string
     */
    public function getVariantDescriptionAttribute()
    {
        return $this->variantValues->pluck('value')->implode(', ');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantValues(): BelongsToMany
    {
        return $this->belongsToMany(VariantValue::class, 'product_variant_details')
            ->withTimestamps();
    }

    public function branchStocks(): HasMany
    {
        return $this->hasMany(BranchStock::class);
    }

    public function productVariantDetails(): HasMany
    {
        return $this->hasMany(ProductVariantDetail::class);
    }
    
    // public function discountCodes()
    // {
    //     return $this->hasMany(DiscountCodeProductVariant::class);
    // }

    /**
     * Get the order items for this product variant.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}