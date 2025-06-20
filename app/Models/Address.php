<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu được liên kết với model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_line',
        'city',
        'district',
        'ward',
        'phone_number',
        'is_default',
        'latitude',
        'longitude',
    ];

    /**
     * Các thuộc tính nên được chuyển đổi sang các kiểu dữ liệu gốc.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean', // Chuyển đổi is_default thành kiểu boolean (true/false)
        'latitude' => 'float',     // Chuyển đổi latitude thành kiểu float
        'longitude' => 'float',    // Chuyển đổi longitude thành kiểu float
    ];

    /**
     * Lấy người dùng sở hữu địa chỉ này.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Lấy địa chỉ đầy đủ được định dạng.
     * Ví dụ: 123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address_line}, {$this->ward}, {$this->district}, {$this->city}";
    }
}