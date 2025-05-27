<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $table = 'product_reviews';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'branch_id',
        'rating',
        'review',
        'review_date',
        'approved',
        'review_image',
        'is_verified_purchase',
        'is_anonymous',
        'helpful_count',
        'report_count',
        'is_featured'
    ];

    protected $casts = [
        'review_date' => 'datetime',
        'approved' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'report_count' => 'integer'
    ];

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order associated with this review.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the branch associated with this review.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}