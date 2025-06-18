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
        'email',
        'password',
        'full_name',
        'phone_number',
        'address',
        'application_id',
        'status',
        'is_available',
        'balance',
        'rating',
        'cancellation_count',
        'reliability_score',
        'penalty_count',
        'auto_deposit_earnings',
        'otp',
        'expires_at',
        'admin_notes',
        'password_reset_at',
        'password_changed_at',
        'must_change_password',
        'updated_by',
        'locked_at',
        'locked_until',
        'locked_by',
        'lock_reason',
        'unlocked_at',
        'unlocked_by',
        'status_changed_at',
        'status_changed_by',
    ];

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'password_reset_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'locked_at' => 'datetime',
        'locked_until' => 'datetime',
        'unlocked_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'is_available' => 'boolean',
        'auto_deposit_earnings' => 'boolean',
        'must_change_password' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // Mutator để tự động hash mật khẩu khi được gán
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Relationships
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

    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }

    public function location()
    {
        return $this->hasOne(DriverLocation::class, 'driver_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    // Accessors lấy data từ bảng documents
    public function getVehicleTypeAttribute()
    {
        return $this->documents?->vehicle_type;
    }

    public function getLicenseClassAttribute()
    {
        return $this->documents?->license_class;
    }

    public function getVehicleTypeLabelAttribute()
    {
        $vehicleType = $this->documents?->vehicle_type;
        return match($vehicleType) {
            'motorbike' => 'Xe máy',
            'car' => 'Ô tô',
            'truck' => 'Xe tải',
            null, '' => null,
            default => ucfirst($vehicleType)
        };
    }

    public function getLicenseClassLabelAttribute()
    {
        $licenseClass = $this->documents?->license_class;
        return match($licenseClass) {
            'A1' => 'A1 - Xe máy dưới 175cc',
            'A2' => 'A2 - Xe máy trên 175cc',
            'B1' => 'B1 - Ô tô dưới 9 chỗ',
            'B2' => 'B2 - Ô tô từ 9-30 chỗ',
            'C'  => 'C - Xe tải',
            null, '' => null,
            default => $licenseClass
        };
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
}
