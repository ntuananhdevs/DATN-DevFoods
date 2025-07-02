<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database.
     *
     * @var string
     */
    protected $table = 'payment_methods';

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the payments for this payment method.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_method_id');
    }

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
} 
