<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_by_role',
        'note',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the status history.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the status text for old status
     */
    public function getOldStatusTextAttribute()
    {
        return $this->getStatusText($this->old_status);
    }

    /**
     * Get the status text for new status
     */
    public function getNewStatusTextAttribute()
    {
        return $this->getStatusText($this->new_status);
    }

    /**
     * Get status text based on status value
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'new' => 'Mới',
            'processing' => 'Đang chuẩn bị',
            'ready' => 'Sẵn sàng',
            'delivery' => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        return $statusMap[$status] ?? ucfirst($status);
    }
} 