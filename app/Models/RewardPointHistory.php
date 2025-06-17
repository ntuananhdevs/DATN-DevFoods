<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPointHistory extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu được liên kết với model.
     *
     * @var string
     */
    protected $table = 'reward_point_histories';

    /**
     * Vì migration của chúng ta chỉ có 'created_at' mà không có 'updated_at',
     * chúng ta cần khai báo hằng số này để Eloquent không tìm cột 'updated_at' khi lưu.
     */
    const UPDATED_AT = null;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'points',
        'reason',
    ];

    /**
     * Các thuộc tính nên được chuyển đổi sang các kiểu dữ liệu gốc.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Lấy người dùng sở hữu lịch sử điểm thưởng này.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lấy đơn hàng (nếu có) liên quan đến lịch sử điểm thưởng này.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}