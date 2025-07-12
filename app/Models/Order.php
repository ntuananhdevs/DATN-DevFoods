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
     * The accessors to append to the model's array form.
     * Thuộc tính này yêu cầu Laravel luôn đính kèm các giá trị từ Accessor
     * vào kết quả JSON, giải quyết lỗi "reading 'bg'".
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'status_color',
        'status_text_color',
        'status_icon',
        'customer_name',
        'customer_phone'
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

    protected function paymentMethodText(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->payment_method) {
                'cod' => 'COD (Thanh toán khi nhận hàng)',
                'vnpay' => 'VNPAY',
                'balance' => 'Số dư tài khoản',
                default => 'Không xác định',
            }
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


    // --- CẬP NHẬT TẠI ĐÂY ---

    // Định nghĩa tĩnh các thuộc tính trạng thái
    private static array $statusAttributes = [
        'awaiting_confirmation' => ['text' => 'Chờ xác nhận', 'bg' => '#fcd34d', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-hourglass-half'], // Vàng nhạt -> Cam đậm
        'awaiting_driver' => ['text' => 'Chờ tài xế', 'bg' => '#f97316', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-user-clock'],       // Cam -> Nâu đỏ
        'driver_assigned' => ['text' => 'Tài xế đã được giao', 'bg' => '#60a5fa', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-clipboard-check'], // Xanh dương nhạt -> Xanh dương đậm
        'driver_confirmed' => ['text' => 'Tài xế đã xác nhận', 'bg' => '#3b82f6', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-check-circle'], // Xanh dương trung bình -> Xanh đậm
        'waiting_driver_pick_up' => ['text' => 'Chờ tài xế lấy hàng', 'bg' => '#818cf8', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-shopping-bag'], // Tím nhạt -> Tím đậm
        'driver_picked_up' => ['text' => 'Đã nhận đơn', 'bg' => '#a78bfa', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-shopping-bag'],     // Tím nhạt -> Tím đậm
        'in_transit' => ['text' => 'Đang giao', 'bg' => '#2dd4bf', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-truck'],               // Xanh ngọc lam -> Xanh lá cây đậm
        'delivered' => ['text' => 'Đã giao', 'bg' => '#4ade80', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-check-double'],            // Xanh lá cây nhạt -> Xanh lá cây đậm hơn
        'item_received' => ['text' => 'Khách đã nhận hàng', 'bg' => '#22c55e', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-home'],      // Xanh lá cây tươi -> Xanh lá cây rất đậm
        'cancelled' => ['text' => 'Đã hủy', 'bg' => '#f87171', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-times-circle'],             // Đỏ nhạt -> Đỏ đậm
        'refunded' => ['text' => 'Đã hoàn tiền', 'bg' => '#a5b4fc', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-undo-alt'],          // Tím xanh nhạt -> Xanh tím đậm
        'payment_failed' => ['text' => 'Thanh toán thất bại', 'bg' => '#ef4444', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-exclamation-triangle'], // Đỏ -> Đỏ sẫm
        'payment_received' => ['text' => 'Thanh toán đã nhận', 'bg' => '#84cc16', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-money-check-alt'], // Xanh lá cây tươi -> Xanh lá cây đậm
        'order_failed' => ['text' => 'Đơn hàng đã thất bại', 'bg' => '#dc2626', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-times-circle'],     // Đỏ đậm -> Đỏ sẫm hơn
        'default' => ['text' => 'Không xác định', 'bg' => '#e5e7eb', 'text_color' => '#FFFFFF', 'icon' => 'fas fa-question-circle'], // Xám nhạt -> Xám đậm
    ];

    /**
     * Lấy text trạng thái Tiếng Việt.
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() => static::$statusAttributes[$this->status]['text'] ?? static::$statusAttributes['default']['text'],
        );
    }

    /**
     * Lấy MÀU HEX (string) cho từng trạng thái.
     * Lưu ý: Phương thức này hiện chỉ trả về màu nền (bg).
     * Nếu bạn cần màu chữ riêng, bạn sẽ cần truy cập $order->status_color['text'] trong Blade.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => static::$statusAttributes[$this->status]['bg'] ?? static::$statusAttributes['default']['bg'],
        );
    }

    /**
     * Lấy class ICON (string) cho từng trạng thái.
     */
    protected function statusIcon(): Attribute
    {
        return Attribute::make(
            get: fn() => static::$statusAttributes[$this->status]['icon'] ?? static::$statusAttributes['default']['icon'],
        );
    }

    /**
     * Lấy text trạng thái tĩnh cho các tab (nếu cần dùng bên ngoài instance của Order)
     */
    public static function getStatusTextStatic(string $status): string
    {
        return static::$statusAttributes[$status]['text'] ?? static::$statusAttributes['default']['text'];
    }

    public static function getStatusIconStatic(string $status): string
    {
        return static::$statusAttributes[$status]['icon'] ?? static::$statusAttributes['default']['icon'];
    }

    protected function statusTextColor(): Attribute
    {
        return Attribute::make(
            get: fn() => static::$statusAttributes[$this->status]['text_color'] ?? static::$statusAttributes['default']['text_color'],
        );
    }
}
