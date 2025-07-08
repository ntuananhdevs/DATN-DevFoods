<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'payer_name',
        'payer_email',
        'payer_phone',
        'txn_ref',
        'transaction_id',
        'response_code',
        'bank_code',
        'payment_amount',
        'payment_currency',
        'payment_status',
        'payment_date',
        'payment_method_detail',
        'gateway_response',
        'ip_address',
        'callback_data'
    ];

    protected $casts = [
        'payment_amount' => 'integer',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the orders for this payment.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payment status text
     */
    public function getPaymentStatusTextAttribute()
    {
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'completed' => 'Thành công',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        return $statusMap[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment is failed
     */
    public function isFailed()
    {
        return $this->payment_status === 'failed';
    }
} 