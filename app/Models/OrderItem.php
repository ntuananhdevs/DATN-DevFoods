<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'product_name_snapshot',
        'variant_name_snapshot',
        'variant_attributes_snapshot',
        'combo_name_snapshot',
        'combo_items_snapshot',
    ];

    protected $appends = [
        'display_product_name',
        'display_product_image',
        'display_variant_name',
        'display_combo_name',
        'display_price',
        'display_total_price'
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
        return $this->product_name_snapshot ?? $this->productVariant?->product?->name ?? $this->combo?->name ?? 'Không xác định';
    }

    /**
     * Get product image (snapshot first, then from relation)
     */
    public function getDisplayProductImageAttribute()
    {
        return $this->product_image_snapshot ?? $this->productVariant?->product?->image ?? $this->combo?->image ?? null;
    }

    /**
     * Get variant name (snapshot first, then from relation)
     */
    public function getDisplayVariantNameAttribute()
    {
        return $this->variant_name_snapshot ?? $this->productVariant?->variant_description ?? null;
    }

    /**
     * Get combo name (snapshot first, then from relation)
     */
    public function getDisplayComboNameAttribute()
    {
        return $this->combo_name_snapshot ?? $this->combo?->name ?? null;
    }

    /**
     * Check if this order item has snapshot data
     */
    public function hasSnapshotData()
    {
        return !empty($this->product_name_snapshot) || !empty($this->combo_name_snapshot);
    }

    /**
     * Get display price based on snapshot data
     */
    public function getDisplayPriceAttribute()
    {
        // Luôn sử dụng unit_price cho mọi trường hợp
        return $this->unit_price;
    }

    protected function displayPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Nếu là biến thể sản phẩm
                if ($this->product_variant_id) {
                    return $this->unit_price; // unit_price nên là giá của biến thể sản phẩm
                }
                // Nếu là combo
                if ($this->combo_id) {
                    // Sử dụng giá combo snapshot nếu có, nếu không thì dùng unit_price của item
                    return $this->combo_price_snapshot ?? $this->unit_price;
                }
                return $this->unit_price; // Giá trị mặc định
            }
        );
    }

    /**
     * Lấy tổng giá của item đơn hàng này, bao gồm cả giá của các topping đi kèm.
     */
    protected function totalPriceWithToppings(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Giá cơ bản của sản phẩm/combo (số lượng * đơn giá)
                $basePrice = $this->unit_price * $this->quantity;

                // Tổng giá của tất cả các topping cho item này
                $toppingsPrice = $this->toppings->sum(function ($topping) {
                    // Sử dụng topping_unit_price_snapshot nếu có, nếu không thì dùng topping->unit_price
                    return ($topping->topping_unit_price_snapshot ?? $topping->unit_price) * $this->quantity;
                });

                // Tổng giá của item bao gồm cả topping
                return $basePrice + $toppingsPrice;
            }
        );
    }
}
