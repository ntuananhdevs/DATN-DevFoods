<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'payment_method',
        'status',
        'description',
        'transaction_code',
        'metadata',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    /**
     * Relation với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . ' VND';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'badge-success',
            'pending' => 'badge-warning', 
            'failed' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    /**
     * Get status text in Vietnamese
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'completed' => 'Hoàn thành',
            'pending' => 'Đang xử lý',
            'failed' => 'Thất bại', 
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    /**
     * Get type text in Vietnamese
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'deposit' => 'Nạp tiền',
            'withdraw' => 'Rút tiền',
            'payment' => 'Thanh toán',
            'refund' => 'Hoàn tiền',
            default => 'Khác'
        };
    }
}
