<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    private static array $statusAttributes = [
        'pending' => [
            'text' => 'Chờ nhận',
            'color' => '#f59e0b', // amber-500
            'icon' => 'fas fa-receipt',
        ],
        'processing' => [
            'text' => 'Đang chuẩn bị',
            'color' => '#3B82F6', // blue-500
            'icon' => 'fas fa-box',
        ],
        'delivering' => [
            'text' => 'Đang giao',
            'color' => '#EA580C', // orange-600
            'icon' => 'fas fa-truck',
        ],
        'shipping' => [
            'text' => 'Đang giao',
            'color' => '#8B5CF6', // violet-500
            'icon' => 'fas fa-shipping-fast',
        ],
        'delivered' => [
            'text' => 'Đã giao',
            'color' => '#16A34A', // green-600
            'icon' => 'fas fa-check-circle',
        ],
        'cancelled' => [
            'text' => 'Đã hủy',
            'color' => '#DC2626', // red-600
            'icon' => 'fas fa-times-circle',
        ],
        'default' => [
            'text' => 'Không xác định',
            'color' => '#6B7280', // gray-500
            'icon' => 'fas fa-question-circle',
        ],
    ];

    /**
     * Lấy text trạng thái Tiếng Việt.
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn () => self::$statusAttributes[$this->status]['text'] ?? self::$statusAttributes['default']['text'],
        );
    }

    /**
     * Lấy MÀU HEX (string) cho từng trạng thái.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => self::$statusAttributes[$this->status]['color'] ?? self::$statusAttributes['default']['color'],
        );
    }

    /**
     * Lấy class ICON (string) cho từng trạng thái.
     */
    protected function statusIcon(): Attribute
    {
        return Attribute::make(
            get: fn () => self::$statusAttributes[$this->status]['icon'] ?? self::$statusAttributes['default']['icon'],
        );
    }

    /**
     * Lấy tên khách hàng (từ User hoặc guest_name)
     */
    protected function customerName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->customer->full_name ?? $this->guest_name ?? 'Khách vãng lai',
        );
    }

    /**
     * Lấy SĐT khách hàng (từ user hoặc guest).
     */
    protected function customerPhone(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->customer->phone ?? $this->guest_phone ?? 'Không có',
        );
    }

    /**
    * Lấy text trạng thái tĩnh cho các tab
    */
    public static function getStatusText(string $status): string
    {
        return self::$statusAttributes[$status]['text'] ?? ucfirst($status);
    }
}
