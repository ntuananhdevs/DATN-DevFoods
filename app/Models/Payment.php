<?php
// TẠO FILE NÀY TẠI: app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
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
        'callback_data',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'gateway_response' => 'array',
        'callback_data' => 'array',
    ];

    /**
     * Lấy phương thức thanh toán của giao dịch này.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Lấy đơn hàng liên quan đến thanh toán này.
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'payment_id');
    }
}