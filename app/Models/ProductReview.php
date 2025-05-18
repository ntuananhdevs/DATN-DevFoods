<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'review',
        'review_date',
        'approved',
        'review_image',
        'is_verified_purchase',
        'is_anonymous',
        'helpful_count',
        'report_count',
        'is_featured',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'approved' => 'boolean',
        'is_featured' => 'boolean',
        'review_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}