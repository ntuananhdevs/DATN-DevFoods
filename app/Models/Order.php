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
        'batch_id',
        'batch_order',
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
        'notes',
        // Snapshot fields cho địa chỉ giao hàng
        'delivery_address_line_snapshot',
        'delivery_ward_snapshot',
        'delivery_district_snapshot',
        'delivery_province_snapshot',
        'delivery_phone_snapshot',
        'delivery_recipient_name_snapshot',
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
     * Get the driver rating for the order.
     */
    public function driverRating()
    {
        return $this->hasOne(DriverRating::class);
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
     * Get the cancellation record for this order.
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
            get: fn() => match ($this->payment->payment_method ?? 'unknown') {
                'cash' => 'Tiền mặt',
                'cod' => 'Thanh toán khi nhận hàng (COD)',
                'vnpay' => 'Thanh toán qua VNPAY',
                'balance' => 'Thanh toán bằng số dư tài khoản',
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
        'pending_payment' => [
            'text' => 'Chưa thanh toán',
            'bg' => '#fef3c7', // Vàng nhạt hơn
            'text_color' => '#92400e',
            'icon' => 'fas fa-credit-card'
        ],
        'awaiting_confirmation' => [
            'text' => 'Chờ xác nhận',
            'bg' => '#fde68a', // Vàng nhạt
            'text_color' => '#78350f',
            'icon' => 'fas fa-hourglass-half'
        ],
        'confirmed' => [
            'text' => 'Đang tìm tài xế',
            'bg' => '#dbeafe', // Xanh dương nhạt
            'text_color' => '#1e40af',
            'icon' => 'fas fa-search'
        ],
        'awaiting_driver' => [
            'text' => 'Chờ tài xế',
            'bg' => '#fcd5ce', // Cam nhạt hồng pastel
            'text_color' => '#7c2d12',
            'icon' => 'fas fa-user-clock'
        ],
        'driver_assigned' => [
            'text' => 'Tài xế đã được giao',
            'bg' => '#cfe0f3', // Xanh dương nhạt
            'text_color' => '#1e3a8a',
            'icon' => 'fas fa-clipboard-check'
        ],
        'driver_confirmed' => [
            'text' => 'Tài xế đã xác nhận',
            'bg' => '#bfdbfe',
            'text_color' => '#1e40af',
            'icon' => 'fas fa-check-circle'
        ],
        'waiting_driver_pick_up' => [
            'text' => 'Chờ tài xế lấy hàng',
            'bg' => '#ddd6fe', // Tím pastel
            'text_color' => '#4c1d95',
            'icon' => 'fas fa-shopping-bag'
        ],
        'driver_picked_up' => [
            'text' => 'Đã nhận đơn',
            'bg' => '#e9d5ff',
            'text_color' => '#6b21a8',
            'icon' => 'fas fa-shopping-bag'
        ],
        'in_transit' => [
            'text' => 'Đang giao',
            'bg' => '#99f6e4', // Xanh ngọc nhạt
            'text_color' => '#134e4a',
            'icon' => 'fas fa-truck'
        ],
        'delivered' => [
            'text' => 'Đã giao',
            'bg' => '#bbf7d0', // Xanh lá nhạt
            'text_color' => '#166534',
            'icon' => 'fas fa-check-double'
        ],
        'item_received' => [
            'text' => 'Khách đã nhận hàng',
            'bg' => '#a7f3d0',
            'text_color' => '#14532d',
            'icon' => 'fas fa-home'
        ],
        'cancelled' => [
            'text' => 'Đã hủy',
            'bg' => '#fecaca', // Đỏ nhạt pastel
            'text_color' => '#7f1d1d',
            'icon' => 'fas fa-times-circle'
        ],
        'refunded' => [
            'text' => 'Đã hoàn tiền',
            'bg' => '#e0e7ff', // Xanh tím nhạt
            'text_color' => '#3730a3',
            'icon' => 'fas fa-undo-alt'
        ],
        'payment_failed' => [
            'text' => 'Thanh toán thất bại',
            'bg' => '#fca5a5',
            'text_color' => '#7f1d1d',
            'icon' => 'fas fa-exclamation-triangle'
        ],
        'payment_received' => [
            'text' => 'Thanh toán đã nhận',
            'bg' => '#dcfce7', // Xanh lá pastel sáng
            'text_color' => '#15803d',
            'icon' => 'fas fa-money-check-alt'
        ],
        'order_failed' => [
            'text' => 'Đơn hàng đã thất bại',
            'bg' => '#fecaca',
            'text_color' => '#991b1b',
            'icon' => 'fas fa-times-circle'
        ],
        'default' => [
            'text' => 'Không xác định',
            'bg' => '#e5e7eb',
            'text_color' => '#374151',
            'icon' => 'fas fa-question-circle'
        ],
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

    /**
     * Accessor methods cho địa chỉ giao hàng
     * Ưu tiên hiển thị snapshot data, fallback về data gốc
     */

    /**
     * Lấy địa chỉ giao hàng hiển thị
     */
    protected function displayDeliveryAddress(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_address_line_snapshot
                ?? $this->delivery_address
                ?? 'Không có địa chỉ'
        );
    }

    /**
     * Lấy phường/xã giao hàng hiển thị
     */
    protected function displayDeliveryWard(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_ward_snapshot
                ?? ($this->address ? $this->address->ward : $this->guest_ward)
                ?? 'Không có'
        );
    }

    /**
     * Lấy quận/huyện giao hàng hiển thị
     */
    protected function displayDeliveryDistrict(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_district_snapshot
                ?? ($this->address ? $this->address->district : $this->guest_district)
                ?? 'Không có'
        );
    }

    /**
     * Lấy tỉnh/thành phố giao hàng hiển thị
     */
    protected function displayDeliveryProvince(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_province_snapshot
                ?? ($this->address ? $this->address->province : $this->guest_city)
                ?? 'Không có'
        );
    }

    /**
     * Lấy số điện thoại giao hàng hiển thị
     */
    protected function displayDeliveryPhone(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_phone_snapshot
                ?? ($this->address ? $this->address->phone : $this->guest_phone)
                ?? 'Không có'
        );
    }

    /**
     * Lấy tên người nhận hàng hiển thị
     */
    protected function displayRecipientName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->delivery_recipient_name_snapshot
                ?? ($this->address ? $this->address->recipient_name : $this->guest_name)
                ?? 'Không có'
        );
    }

    /**
     * Lấy địa chỉ đầy đủ để hiển thị
     */
    protected function displayFullDeliveryAddress(): Attribute
    {
        return Attribute::make(
            get: fn() => trim(implode(', ', array_filter([
                $this->display_delivery_address,
                $this->display_delivery_ward,
                $this->display_delivery_district,
                $this->display_delivery_province
            ])))
        );
    }

    /**
     * Kiểm tra xem có dữ liệu snapshot địa chỉ không
     */
    public function hasAddressSnapshot(): bool
    {
        return !empty($this->delivery_address_line_snapshot) ||
            !empty($this->delivery_ward_snapshot) ||
            !empty($this->delivery_district_snapshot) ||
            !empty($this->delivery_province_snapshot);
    }

    protected function calculatedSubtotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->orderItems->sum(function ($item) {
                    // Gọi accessor totalPriceWithToppings từ OrderItem
                    return $item->totalPriceWithToppings;
                });
            }
        );
    }

    // Đảm bảo total_amount được tính dựa trên calculatedSubtotal + delivery_fee - discount_amount
    // Bạn có thể update cột total_amount trong DB khi tạo/cập nhật đơn hàng, hoặc cũng có thể là một accessor
    // Ví dụ:
    protected function calculatedTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->calculatedSubtotal + $this->delivery_fee - $this->discount_amount
        );
    }

    /**
     * Lấy các đơn hàng có thể ghép với đơn hàng này
     * Chỉ cần cùng tài xế và có ít nhất 2 đơn
     */
    public function getBatchableOrders()
    {
        if (!$this->driver_id) {
            return collect();
        }

        // Lấy tất cả đơn hàng khác của cùng tài xế
        return static::where('driver_id', $this->driver_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', ['driver_assigned', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])
            ->with(['address'])
            ->get();
    }

    /**
     * Kiểm tra xem đơn hàng này có đang trong một batch hay không
     */
    public function isPartOfBatch(): bool
    {
        // Nếu đơn hàng đã giao hoặc bị hủy thì không còn trong batch
        if (in_array($this->status, ['delivered', 'cancelled'])) {
            return false;
        }
        
        // Kiểm tra xem có ít nhất 2 đơn hàng của cùng tài xế hay không
        $batchOrders = $this->getBatchOrders();
        return $batchOrders->count() > 1;
    }



    /**
     * Lấy ID nhóm đơn ghép dựa trên tài xế và thời gian
     */
    public function getBatchGroupId(): string
    {
        return 'BATCH_' . $this->driver_id . '_' . $this->updated_at->format('YmdH');
    }

    /**
     * Lấy tất cả đơn hàng trong cùng nhóm ghép
     */
    public function getBatchOrders()
    {
        if (!$this->driver_id) {
            return collect([$this]);
        }

        $batchableOrders = $this->getBatchableOrders();
        
        // Chỉ thêm đơn hàng hiện tại nếu nó chưa được giao
        if (in_array($this->status, ['driver_assigned', 'driver_confirmed', 'waiting_driver_pick_up', 'driver_picked_up'])) {
            $batchableOrders->prepend($this);
        }
        
        return $batchableOrders->sortBy('id');
    }
}
