<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeProduct extends Model
{
    use HasFactory;
    protected $table = 'discount_code_products';

    protected $fillable = ['discount_code_id', 'product_id', 'category_id', 'combo_id', 'product_variant_id'];

    // Mối quan hệ
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
    
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}