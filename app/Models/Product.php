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
        'sku',
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
        return Attribute::whereIn('id', function ($query) {
            $query->select('attribute_id')
                ->from('attribute_values')
                ->whereIn('id', function ($subQuery) {
                    $subQuery->select('attribute_value_id')
                        ->from('product_variant_values')
                        ->whereIn('product_variant_id', $this->variants()->pluck('id'));
                });
        })->distinct();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}