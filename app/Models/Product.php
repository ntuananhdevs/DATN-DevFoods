<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'base_price',
        'available',
        'preparation_time',
        'status',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
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

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function branchStocks()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImg::class);
    }
}