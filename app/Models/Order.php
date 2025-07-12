<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'branch_id',
        'driver_id',
        'address_id',
        'discount_code_id',
        'payment_id', // Thêm payment_id vào fillable
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
     * === THÊM KHỐI CODE NÀY VÀO MODEL CỦA BẠN ===
     *
     * The accessors to append to the model's array form.
     * Thuộc tính này yêu cầu Laravel luôn đính kèm các giá trị từ Accessor
     * vào kết quả JSON, giải quyết lỗi "reading 'bg'".
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'status_color',
        'status_icon',
        'customer_name',
        'customer_phone',
        'payment_method_text',
        'payment_status'
    ];

    /**
     * Get the status history for the order.
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the cancellation information for the order.
     */
    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if order has cancellation record
     */
    public function hasCancellation()
    {
        return $this->cancellation()->exists();
    }

    /**
     * Get the customer name (either from User or guest_name)
     */
    private static array $statusAttributes = [
        'awaiting_confirmation' => ['text' => 'Chờ xác nhận', 'bg' => '#fef9c3', 'text_color' => '#ca8a04', 'icon' => 'fas fa-hourglass-half'],
        'confirmed' => ['text' => 'Đã xác nhận', 'bg' => '#dbeafe', 'text_color' => '#2563eb', 'icon' => 'fas fa-check'],
        'awaiting_driver' => ['text' => 'Chờ tài xế', 'bg' => '#ffedd5', 'text_color' => '#c2410c', 'icon' => 'fas fa-user-clock'],

        'driver_picked_up' => ['text' => 'Đã nhận đơn', 'bg' => '#e0e7ff', 'text_color' => '#4338ca', 'icon' => 'fas fa-shopping-bag'],
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
            get: fn() => self::$statusAttributes[$this->status]['text'] ?? self::$statusAttributes['default']['text'],
        );
    }

    /**
     * Lấy MÀU HEX (string) cho từng trạng thái.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => [
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
            get: fn() => self::$statusAttributes[$this->status]['icon'] ?? self::$statusAttributes['default']['icon'],
        );
    }

    /**
     * Lấy tên khách hàng (từ User hoặc guest_name)
     */
    protected function customerName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->customer->full_name ?? $this->guest_name ?? 'Khách vãng lai',
        );
    }

    /**
     * Lấy SĐT khách hàng (từ user hoặc guest).
     */
    protected function customerPhone(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->customer->phone ?? $this->guest_phone ?? 'Không có',
        );
    }

    /**
     * Lấy text trạng thái tĩnh cho các tab
     *
     * Lấy text phương thức thanh toán
     */
    protected function paymentMethodText(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->payment ? match ($this->payment->payment_method) {
                'cod' => 'COD (Thanh toán khi nhận hàng)',
                'vnpay' => 'VNPAY',
                'balance' => 'Số dư tài khoản',
                default => 'Không xác định',
            } : 'Chưa có thanh toán'
        );
    }

    /**
     * Lấy trạng thái thanh toán
     */
    protected function paymentStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->payment->payment_status ?? 'pending'
        );
    }

    /**
     * Lấy text trạng thái tĩnh cho các tab
     */
    public static function getStatusText(string $status): string
    {
        return self::$statusAttributes[$status]['text'] ?? ucfirst($status);
    }

    public function getStatusSvgIconAttribute(): string
    {
        $iconClass = 'w-6 h-6'; // Kích thước icon bên trong hình tròn
        $svg = '';

        switch ($this->status) {
            case 'awaiting_confirmation':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hourglass ' . $iconClass . '"><path d="M6 2v6a6 6 0 0 0 6 6 6 6 0 0 0 6-6V2"></path><path d="M6 22v-6a6 6 0 0 1 6-6 6 6 0 0 1 6 6v6"></path></svg>';
                break;
            case 'awaiting_driver':
            case 'accepted':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package ' . $iconClass . '"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>';
                break;
            case 'driver_picked_up':
            case 'in_transit':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck ' . $iconClass . '"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 2 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>';
                break;
            case 'delivered':
            case 'item_received':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big ' . $iconClass . '"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>';
                break;
            case 'cancelled':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle ' . $iconClass . '"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>';
                break;
            case 'refunded':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wallet-2 ' . $iconClass . '"><path d="M17 14h.01"></path><path d="M7 14h.01"></path><path d="M22 7H2v13a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2Z"></path><path d="M2 7V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3"></path></svg>';
                break;
            default:
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info ' . $iconClass . '"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>';
                break;
        }
        return $svg;
    }

    /**
     * Get the payment associated with the order.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
