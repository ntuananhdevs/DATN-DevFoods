<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'license_number',
        'vehicle_type', 'vehicle_registration', 'vehicle_color',
        'status', 'is_available', 'current_latitude', 'current_longitude',
        'balance', 'rating', 'cancellation_count', 'reliability_score',
        'penalty_count', 'auto_deposit_earnings', 'email', 'password',
        'phone_number', 'full_name'
    ];

    protected $casts = [
        'application_id' => 'integer',
        'is_available' => 'boolean',
        'auto_deposit_earnings' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8'
    ];

    protected $hidden = [
        'password',
    ];

    // Mutator để tự động hash mật khẩu khi được gán
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(DriverApplication::class, 'application_id');
    }
}
