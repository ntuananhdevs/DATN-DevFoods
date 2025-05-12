<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'driver_application_id', 'driver_license_number',
        'vehicle_type', 'vehicle_registration', 'vehicle_color',
        'status', 'is_available', 'current_latitude', 'current_longitude',
        'balance', 'rating', 'cancellation_count', 'reliability_score',
        'penalty_count', 'auto_deposit_earnings'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'auto_deposit_earnings' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(DriverApplication::class, 'driver_application_id');
    }
}