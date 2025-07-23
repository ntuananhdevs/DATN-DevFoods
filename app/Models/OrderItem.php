<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'combo_id',
        'quantity',
        'unit_price',
        'total_price',
        // Snapshot fields
        'product_name',
        'product_sku',
        'product_description',
        'product_image',
        'variant_name',
        'variant_attributes',
        'variant_price',
        'combo_name',
        'combo_description',
        'combo_image',
        'combo_items',
        'combo_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'variant_attributes' => 'array',
        'combo_items' => 'array',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product variant associated with the item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the combo associated with the item.
     */
    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    /**
     * Get the toppings for this order item.
     */
    public function toppings()
    {
        return $this->hasMany(OrderItemTopping::class);
    }

    /**
     * Get the product through product variant.
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductVariant::class, 'id', 'id', 'product_variant_id', 'product_id');
    }

    /**
     * Get product name (snapshot first, then from relation)
     */
    public function getDisplayProductNameAttribute()
    {
        return $this->product_name ?? $this->productVariant?->product?->name ?? $this->combo?->name ?? 'Không xác định';
    }

    /**
     * Get product image (snapshot first, then from relation)
     */
    public function getDisplayProductImageAttribute()
    {
        return $this->product_image ?? $this->productVariant?->product?->image ?? $this->combo?->image ?? null;
    }

    /**
     * Get variant name (snapshot first, then from relation)
     */
    public function getDisplayVariantNameAttribute()
    {
        return $this->variant_name ?? $this->productVariant?->name ?? null;
    }

    /**
     * Get combo name (snapshot first, then from relation)
     */
    public function getDisplayComboNameAttribute()
    {
        return $this->combo_name ?? $this->combo?->name ?? null;
    }

    /**
     * Check if this order item has snapshot data
     */
    public function hasSnapshotData()
    {
        return !empty($this->product_name) || !empty($this->combo_name);
    }
}
