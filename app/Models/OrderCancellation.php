<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cancelled_by',
        'cancellation_type',
        'cancellation_date',
        'reason',
        'cancellation_stage',
        'penalty_applied',
        'penalty_amount',
        'points_deducted',
        'evidence',
        'notes'
    ];

    protected $casts = [
        'cancellation_date' => 'datetime',
        'penalty_applied' => 'boolean',
        'penalty_amount' => 'decimal:2',
        'points_deducted' => 'integer',
    ];

    /**
     * Get the order that was cancelled.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who cancelled the order.
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get the cancellation type text
     */
    public function getCancellationTypeTextAttribute()
    {
        $typeMap = [
            'customer_cancel' => 'Khách hàng hủy',
            'driver_cancel' => 'Tài xế hủy',
            'restaurant_cancel' => 'Nhà hàng hủy',
            'system_cancel' => 'Hệ thống hủy'
        ];

        return $typeMap[$this->cancellation_type] ?? ucfirst($this->cancellation_type);
    }

    /**
     * Get the cancellation stage text
     */
    public function getCancellationStageTextAttribute()
    {
        $stageMap = [
            'before_processing' => 'Trước khi chế biến',
            'processing' => 'Đang chế biến',
            'ready_for_delivery' => 'Sẵn sàng giao',
            'during_delivery' => 'Đang giao hàng'
        ];

        return $stageMap[$this->cancellation_stage] ?? ucfirst($this->cancellation_stage);
    }

    /**
     * Check if penalty was applied
     */
    public function hasPenalty()
    {
        return $this->penalty_applied && $this->penalty_amount > 0;
    }

    /**
     * Check if points were deducted
     */
    public function hasPointsDeducted()
    {
        return $this->points_deducted > 0;
    }
} 