<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountUsageHistory extends Model
{
    use HasFactory;
    protected $table = 'discount_usage_history';

    protected $fillable = [
        'discount_code_id', 'order_id', 'user_id', 'branch_id', 'guest_phone',
        'original_amount', 'discount_amount', 'used_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime'
    ];

    // Mối quan hệ
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}