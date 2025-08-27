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
        'processed_at',
        'expires_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'expires_at' => 'datetime'
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
            'expired' => 'badge-dark',
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
            'expired' => 'Hết hạn',
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

    /**
     * Get time remaining in seconds for pending transactions
     */
    public function getTimeRemainingSecondsAttribute()
    {
        if ($this->status !== 'pending' || !$this->expires_at) {
            return 0;
        }

        $now = now();
        if ($this->expires_at <= $now) {
            return 0;
        }

        return $this->expires_at->diffInSeconds($now);
    }

    /**
     * Check if transaction is expired
     */
    public function getIsExpiredAttribute()
    {
        if ($this->status !== 'pending' || !$this->expires_at) {
            return $this->status === 'expired';
        }

        return $this->expires_at <= now();
    }

    /**
     * Check if transaction can be retried
     */
    public function getCanRetryAttribute()
    {
        return $this->status === 'pending' 
            && $this->type === 'deposit' 
            && !$this->is_expired 
            && $this->time_remaining_seconds > 0;
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for expired transactions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for transactions that should be expired (past expires_at)
     */
    public function scopeShouldBeExpired($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /**
     * Auto-expire this transaction if needed
     */
    public function autoExpireIfNeeded()
    {
        if ($this->status === 'pending' && $this->expires_at && $this->expires_at <= now()) {
            $this->update(['status' => 'expired']);
            return true;
        }
        return false;
    }

    /**
     * Scope for withdrawal transactions
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdraw');
    }

    /**
     * Scope for deposit transactions
     */
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope for pending withdrawals
     */
    public function scopePendingWithdrawals($query)
    {
        return $query->where('type', 'withdraw')->where('status', 'pending');
    }

    /**
     * Scope for completed withdrawals
     */
    public function scopeCompletedWithdrawals($query)
    {
        return $query->where('type', 'withdraw')->where('status', 'completed');
    }

    /**
     * Get bank information from metadata
     */
    public function getBankInfoAttribute()
    {
        if ($this->type !== 'withdraw' || !$this->metadata) {
            return null;
        }

        return [
            'bank_name' => $this->metadata['bank_name'] ?? null,
            'bank_account' => $this->metadata['bank_account'] ?? null,
            'account_holder' => $this->metadata['account_holder'] ?? null,
        ];
    }

    /**
     * Check if withdrawal can be processed automatically
     */
    public function canAutoProcess()
    {
        return $this->type === 'withdraw' 
            && $this->status === 'pending'
            && $this->amount <= config('wallet.auto_process_limit', 1000000); // 1M VND
    }

    /**
     * Check if withdrawal requires admin approval
     */
    public function requiresAdminApproval()
    {
        return $this->type === 'withdraw' 
            && $this->status === 'pending'
            && $this->amount > config('wallet.auto_process_limit', 1000000);
    }

    /**
     * Validate bank account information
     */
    public function validateBankInfo()
    {
        if ($this->type !== 'withdraw') {
            return true;
        }

        $bankInfo = $this->bank_info;
        
        if (!$bankInfo || !$bankInfo['bank_name'] || !$bankInfo['bank_account'] || !$bankInfo['account_holder']) {
            return false;
        }

        // Validate bank account number format
        $bankAccount = preg_replace('/\D/', '', $bankInfo['bank_account']);
        if (strlen($bankAccount) < 8 || strlen($bankAccount) > 20) {
            return false;
        }

        return true;
    }

    /**
     * Get withdrawal processing fee
     */
    public function getProcessingFeeAttribute()
    {
        if ($this->type !== 'withdraw') {
            return 0;
        }

        // Fee structure based on amount
        if ($this->amount <= 500000) {
            return 5000; // 5K VND for amounts <= 500K
        } elseif ($this->amount <= 2000000) {
            return 10000; // 10K VND for amounts <= 2M
        } else {
            return 15000; // 15K VND for amounts > 2M
        }
    }

    /**
     * Get net amount after processing fee
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->processing_fee;
    }
}
