<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'image',
        'description',
        'original_price',
        'price',
        'quantity',
        'status',
        'category_id',
        'created_by',
        'updated_by'
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

    // Thêm vào Model Combo:
    protected $appends = ['image_url', 'total_products', 'status_text'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('s3')->url($this->image);
        }
        return asset('images/default-combo.png');
    }

    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'selling':
                return 'Đang bán';
            case 'coming_soon':
                return 'Sắp bán';
            case 'discontinued':
                return 'Dừng bán';
            default:
                return 'Không xác định';
        }
    }

    public function getTotalOriginalPriceAttribute()
    {
        // Tính tổng giá gốc của combo dựa trên các biến thể sản phẩm và số lượng từng biến thể
        return $this->productVariants()->get()->sum(function($variant) {
            $quantity = $variant->pivot->quantity ?? 1;
            return $variant->price * $quantity;
        });
    }

    public function getDiscountPercentAttribute()
    {
        $originalPrice = $this->total_original_price;
        if ($originalPrice > 0) {
            return round((($originalPrice - $this->price) / $originalPrice) * 100, 2);
        }
        return 0;
    }

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with ComboItems
    public function comboItems()
    {
        return $this->hasMany(ComboItem::class);
    }

    // Relationships for created_by and updated_by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function comboBranchStocks()
    {
        return $this->hasMany(\App\Models\ComboBranchStock::class, 'combo_id');
    }
}
