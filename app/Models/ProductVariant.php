<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'image',
        'stock_quantity',
        'active',
    ];

    /**
     * Lấy sản phẩm của biến thể
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy giá trị biến thể
     */
    public function variantValues()
    {
        return $this->hasMany(VariantValue::class);
    }
}