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
        'combo_id',
        'order_id',
        'branch_id',
        'rating',
        'review',
        'review_date',
        'review_image',
        'is_verified_purchase',
        'helpful_count',
        'report_count',
        'is_featured'
    ];

    protected $casts = [
        'review_date' => 'datetime',
        'is_verified_purchase' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'report_count' => 'integer'
    ];

    protected $appends = ['purchased_variant_attributes'];

    /**
     * Get the user who wrote the review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer who wrote the review (alias for user).
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the product being reviewed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the combo being reviewed.
     */
    public function combo()
    {
        return $this->belongsTo(Combo::class);
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

    public function replies()
    {
        return $this->hasMany(\App\Models\ReviewReply::class, 'review_id');
    }

    public function reports()
    {
        return $this->hasMany(\App\Models\ReviewReport::class, 'review_id');
    }

    public function getPurchasedVariantAttributesAttribute()
    {
        // Nếu không có order_id thì không có thông tin
        if (!$this->order_id || !$this->product_id) return [];
        $order = $this->order;
        if (!$order) return [];
        // Lấy order item đúng product_id (có thể có nhiều item cùng product_id, lấy tất cả)
        $items = $order->orderItems->where('productVariant.product_id', $this->product_id);
        $result = [];
        foreach ($items as $item) {
            $variant = $item->productVariant;
            if ($variant) {
                foreach ($variant->variantValues as $value) {
                    $result[] = [
                        'name' => $value->attribute ? $value->attribute->name : '',
                        'value' => $value->value
                    ];
                }
            }
        }
        // Loại bỏ trùng lặp thuộc tính (nếu có nhiều item cùng product_id)
        $result = collect($result)->unique(function($v) {
            return $v['name'] . ':' . $v['value'];
        })->values()->all();
        return $result;
    }
}