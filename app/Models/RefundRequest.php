<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RefundRequest extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // Refund type constants
    const TYPE_FULL = 'full';
    const TYPE_PARTIAL = 'partial';

    protected $fillable = [
        'refund_code',
        'order_id',
        'customer_id',
        'branch_id',
        'refund_amount',
        'refund_type',
        'reason',
        'customer_message',
        'attachments',
        'status',
        'admin_note',
        'processed_by',
        'processed_at',
        'completed_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refundRequest) {
            if (empty($refundRequest->refund_code)) {
                $refundRequest->refund_code = 'RF' . date('Ymd') . strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Get status options for display
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_APPROVED => 'Đã duyệt',
            self::STATUS_REJECTED => 'Từ chối',
            self::STATUS_COMPLETED => 'Hoàn thành'
        ];
    }

    /**
     * Get refund type options
     */
    public static function getRefundTypeOptions()
    {
        return [
            self::TYPE_FULL => 'Hoàn tiền toàn bộ',
            self::TYPE_PARTIAL => 'Hoàn tiền một phần'
        ];
    }

    /**
     * Relationship with Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with Customer (User)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship with Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship with processed by user
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Relationship with chat messages related to this refund
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'related_order_id', 'order_id')
                    ->where('is_system_message', true)
                    ->where('message', 'like', '%hoàn tiền%');
    }

    /**
     * Get conversation related to this refund request
     */
    public function getConversation()
    {
        return Conversation::where('customer_id', $this->customer_id)
                          ->where('branch_id', $this->branch_id)
                          ->where('status', '!=', Conversation::STATUS_CLOSED)
                          ->first();
    }

    /**
     * Create or get conversation for refund discussion
     */
    public function createOrGetConversation()
    {
        $conversation = $this->getConversation();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_id' => $this->customer_id,
                'branch_id' => $this->branch_id,
                'status' => Conversation::STATUS_OPEN
            ]);
        }
        
        return $conversation;
    }

    /**
     * Send system message about refund status change
     */
    public function sendStatusUpdateMessage($oldStatus = null)
    {
        $conversation = $this->createOrGetConversation();
        
        $statusMessages = [
            self::STATUS_PENDING => 'Yêu cầu hoàn tiền #{refund_code} đã được tạo và đang chờ xử lý.',
            self::STATUS_PROCESSING => 'Yêu cầu hoàn tiền #{refund_code} đang được xử lý.',
            self::STATUS_APPROVED => 'Yêu cầu hoàn tiền #{refund_code} đã được duyệt. Số tiền {amount}đ sẽ được hoàn vào tài khoản của bạn.',
            self::STATUS_REJECTED => 'Yêu cầu hoàn tiền #{refund_code} đã bị từ chối. Lý do: {admin_note}',
            self::STATUS_COMPLETED => 'Yêu cầu hoàn tiền #{refund_code} đã hoàn thành. Số tiền {amount}đ đã được cộng vào số dư tài khoản của bạn.'
        ];
        
        $message = $statusMessages[$this->status] ?? 'Trạng thái yêu cầu hoàn tiền đã được cập nhật.';
        $message = str_replace([
            '{refund_code}',
            '{amount}',
            '{admin_note}'
        ], [
            $this->refund_code,
            number_format($this->refund_amount, 0, ',', '.'),
            $this->admin_note ?? 'Không có ghi chú'
        ], $message);
        
        return ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->processed_by ?? 1, // System user ID
            'receiver_id' => $this->customer_id,
            'sender_type' => 'system',
            'receiver_type' => 'customer',
            'message' => $message,
            'status' => 'sent',
            'is_system_message' => true,
            'related_order_id' => $this->order_id,
            'branch_id' => $this->branch_id,
            'sent_at' => now()
        ]);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope for filtering by branch
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get refund type label for display
     */
    public function getRefundTypeLabelAttribute()
    {
        return self::getRefundTypeOptions()[$this->refund_type] ?? $this->refund_type;
    }

    /**
     * Check if refund can be processed
     */
    public function canBeProcessed()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if refund is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if refund is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }
}