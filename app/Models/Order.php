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

    public function items()
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
        'awaiting_confirmation' => ['text' => 'Chờ xác nhận', 'bg' => '#fef9c3', 'text_color' => '#ca8a04', 'icon' => 'fas fa-hourglass-half'],
        'confirmed' => ['text' => 'Đã xác nhận', 'bg' => '#dbeafe', 'text_color' => '#2563eb', 'icon' => 'fas fa-check'],
        'awaiting_driver' => ['text' => 'Chờ tài xế', 'bg' => '#ffedd5', 'text_color' => '#c2410c', 'icon' => 'fas fa-user-clock'],
        'driver_picked_up' => ['text' => 'Tài xế đã nhận', 'bg' => '#e0e7ff', 'text_color' => '#4338ca', 'icon' => 'fas fa-shopping-bag'],
        'in_transit' => ['text' => 'Đang giao', 'bg' => '#ccfbf1', 'text_color' => '#0f766e', 'icon' => 'fas fa-truck'],
        'delivered' => ['text' => 'Đã giao', 'bg' => '#dcfce7', 'text_color' => '#16a34a', 'icon' => 'fas fa-check-double'],
        'item_received' => ['text' => 'Đã nhận hàng', 'bg' => '#d1fae5', 'text_color' => '#047857', 'icon' => 'fas fa-home'],
        'cancelled' => ['text' => 'Đã hủy', 'bg' => '#fee2e2', 'text_color' => '#dc2626', 'icon' => 'fas fa-times-circle'],
        'failed_delivery' => ['text' => 'Giao thất bại', 'bg' => '#fee2e2', 'text_color' => '#dc2626', 'icon' => 'fas fa-exclamation-triangle'],
        'delivery_incomplete' => ['text' => 'Giao chưa hoàn tất', 'bg' => '#fef3c7', 'text_color' => '#d97706', 'icon' => 'fas fa-exclamation-circle'],
        'pending_refund' => ['text' => 'Chờ hoàn tiền', 'bg' => '#e0f2fe', 'text_color' => '#0284c7', 'icon' => 'fas fa-undo-alt'],
        'investigating' => ['text' => 'Đang điều tra', 'bg' => '#e5e7eb', 'text_color' => '#4b5563', 'icon' => 'fas fa-search'],
        'waiting_for_confirmation' => ['text' => 'Đang chờ xác nhận', 'bg' => '#fef9c3', 'text_color' => '#ca8a04', 'icon' => 'fas fa-hourglass-half'],
        'default' => ['text' => 'Không xác định', 'bg' => '#f3f4f6', 'text_color' => '#4b5563', 'icon' => 'fas fa-question-circle'],
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
            get: fn () => [
                'bg' => self::$statusAttributes[$this->status]['bg'] ?? self::$statusAttributes['default']['bg'],
                'text' => self::$statusAttributes[$this->status]['text_color'] ?? self::$statusAttributes['default']['text_color'],
            ]
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
