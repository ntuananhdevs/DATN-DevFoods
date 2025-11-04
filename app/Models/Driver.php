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
        'last_active_at',
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
        'last_active_at' => 'datetime',
        'is_available' => 'boolean',
        'auto_deposit_earnings' => 'boolean',
        'must_change_password' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // Mutator để tự động hash mật khẩu khi được gán
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }
    
    // Accessor để lấy trạng thái chi tiết của tài xế
    public function getDriverStatusAttribute()
    {
        // Kiểm tra xem tài xế có đơn hàng đang giao không
        // Chỉ xem là đang giao khi tài xế đã lấy hàng (driver_picked_up) hoặc đang giao (in_transit)
        $hasActiveDelivery = $this->orders()
            ->whereIn('status', ['driver_picked_up', 'in_transit'])
            ->exists();
            
        if ($hasActiveDelivery) {
            return 'delivering';
        }
        
        // Nếu không có đơn hàng đang giao, dựa vào trạng thái is_available
        return $this->is_available ? 'available' : 'offline';
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

    public function locations()
    {
        return $this->hasMany(\App\Models\DriverLocation::class);
    }

    public function location()
    {
        return $this->hasOne(\App\Models\DriverLocation::class)->latestOfMany();
    }
    
    public function ratings()
    {
        return $this->hasMany(DriverRating::class);
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
    
    // Các phương thức getIsOnlineAttribute() và getDriverStatusAttribute() đã được định nghĩa ở trên
    
    /**
     * Cập nhật thống kê đánh giá của tài xế
     * Tính toán lại điểm đánh giá trung bình dựa trên tất cả các đánh giá
     */
    public function updateRatingStatistics()
    {
        $ratings = $this->ratings()->pluck('rating');
        
        if ($ratings->count() > 0) {
            $this->rating = $ratings->avg();
            $this->save();
        }
        
        return $this->rating;
    }
}
