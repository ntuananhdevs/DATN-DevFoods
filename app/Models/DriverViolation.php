<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'violation_type',
        'description',
        'severity',
        'penalty_amount',
        'reported_by',
        'reported_at',
        'status',
        'resolution_notes',
        'resolved_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'penalty_amount' => 'decimal:2'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getSeverityLabelAttribute()
    {
        return match($this->severity) {
            'low' => 'Nhẹ',
            'medium' => 'Trung bình',
            'high' => 'Nghiêm trọng',
            'critical' => 'Rất nghiêm trọng',
            default => 'Không xác định'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Đang xử lý',
            'resolved' => 'Đã giải quyết',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        };
    }
}
