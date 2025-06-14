<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Notifications\Notifiable;

class Driver extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait, Notifiable;

    protected $fillable = [
        'application_id',
        'license_number',
        'license_class',
        'license_expiry',
        'license_plate',
        'vehicle_type',
        'vehicle_registration',
        'vehicle_color',
        'status',
        'is_available',
        'current_latitude',
        'current_longitude',
        'balance',
        'rating',
        'cancellation_count',
        'reliability_score',
        'penalty_count',
        'auto_deposit_earnings',
        'email',
        'password',
        'phone_number',
        'full_name',
        'address',
        'id_card_front',
        'id_card_back',
        'license_front',
        'license_back',
        'admin_notes',
        'password_reset_at',
        'password_changed_at',
        'must_change_password',
        'updated_by',
        'email_verified_at',
        'remember_token',
        'expires_at',
        'otp_code',
        'locked_at',
        'locked_until',
        'locked_by',
        'lock_reason',
        'unlocked_at',
        'unlocked_by',
        'status_changed_at',
        'status_changed_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_reset_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'license_expiry' => 'date',
        'expires_at' => 'datetime',
        'locked_at' => 'datetime',
        'locked_until' => 'datetime',
        'unlocked_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'is_available' => 'boolean',
        'auto_deposit_earnings' => 'boolean',
        'must_change_password' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    // Mutator để tự động hash mật khẩu khi được gán
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(DriverApplication::class, 'application_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function violations()
    {
        return $this->hasMany(DriverViolation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function unlockedBy()
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    public function statusChangedBy()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByVehicleType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Đang hoạt động',
            'inactive' => 'Không hoạt động',
            'locked' => 'Bị khóa',
            default => 'Không xác định'
        };  
    }

    public function getVehicleTypeLabelAttribute()
    {
        return match($this->vehicle_type) {
            'motorbike' => 'Xe máy',
            'car' => 'Ô tô',
            'truck' => 'Xe tải',
            default => ucfirst($this->vehicle_type)
        };
    }

    public function getLicenseClassLabelAttribute()
    {
        return match($this->license_class) {
            'A1' => 'A1 - Xe máy dưới 175cc',
            'A2' => 'A2 - Xe máy trên 175cc',
            'B1' => 'B1 - Ô tô dưới 9 chỗ',
            'B2' => 'B2 - Ô tô từ 9-30 chỗ',
            'C' => 'C - Xe tải',
            default => $this->license_class
        };
    }
}
