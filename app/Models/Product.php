<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'base_price',
        'preparation_time',
        'ingredients',
        'short_description',
        'description',
        'status',
        'release_at',
        'is_featured',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'release_at' => 'datetime',
        'is_featured' => 'boolean',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImg::class, 'product_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function branchStocks()
    {
        return $this->hasManyThrough(BranchStock::class, ProductVariant::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attributes()
    {
        return $this->hasManyThrough(
            VariantAttribute::class,
            ProductVariant::class,
            'product_id', // Foreign key on product_variants table
            'id', // Foreign key on variant_attributes table
            'id', // Local key on products table
            'id' // Local key on product_variants table
        )->distinct();
    }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'product_toppings')
            ->withTimestamps();
    }

    public function isActiveInBranch($branchId)
    {
        // Kiểm tra xem sản phẩm có variant nào được áp dụng tại chi nhánh này không
        return $this->variants()
            ->whereHas('branchStocks', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->exists();
    }

    // app/Models/Product.php
    public function wishlist()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImg::class, 'product_id')->where('is_primary', true);
    }
}
