<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ProductFactory::new();
    }
    
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'base_price',
        'stock',
        'image',
        'preparation_time',
    ];

    /**
     * Lấy danh mục của sản phẩm
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Lấy các biến thể của sản phẩm
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // /**
    //  * Lấy các đánh giá của sản phẩm
    //  */
    // public function reviews()
    // {
    //     return $this->hasMany(ProductReview::class);
    // }
}
