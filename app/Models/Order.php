<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'driver_id',
        'address_id',
        'discount_code_id',
        'payment_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_address',
        'guest_ward',
        'guest_district',
        'guest_city',
        'guest_latitude',
        'guest_longitude',
        'estimated_delivery_time',
        'actual_delivery_time',
        'delivery_fee',
        'driver_earning',
        'discount_amount',
        'tax_amount',
        'order_date',
        'delivery_date',
        'status',
        'points_earned',
        'subtotal',
        'total_amount',
        'delivery_address',
        'notes'
    ];

    protected $casts = [
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
        'guest_latitude' => 'decimal:8',
        'guest_longitude' => 'decimal:8',
        'delivery_fee' => 'decimal:2',
        'driver_earning' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the branch associated with the order.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the driver associated with the order.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the address associated with the order.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the payment associated with the order.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the discount code associated with the order.
     */
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get the items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for the order.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the customer name (either from User or guest_name)
     */
    public function getCustomerNameAttribute()
    {
        if ($this->customer) {
            return $this->customer->name;
        }
        return $this->guest_name ?? 'Khách vãng lai';
    }

    public function getStatusTextAttribute()
    {
        // Chuyển đổi status từ 'delivered' -> 'Đã giao'
        return match ($this->status) {
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };
    }

    public function getStatusColorAttribute()
    {
        // Trả về màu nền và màu chữ cho từng trạng thái
        return match ($this->status) {
            'pending' => ['bg' => '#fef3c7', 'text' => '#b45309'], // yellow
            'processing' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'], // blue
            'shipping' => ['bg' => '#e0e7ff', 'text' => '#4338ca'], // indigo
            'delivered' => ['bg' => '#d1fae5', 'text' => '#065f46'], // green
            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'], // red
            default => ['bg' => '#f3f4f6', 'text' => '#374151'], // gray
        };
    }
}
