<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    // Định nghĩa các status constants

    const STATUS_OPEN = 'open';
    const STATUS_DISTRIBUTED = 'distributed';
    const STATUS_ACTIVE = 'active';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'customer_id',
        'branch_id',
        'status',
        'distribution_time',
        'is_distributed'
    ];

    protected $casts = [
        'distribution_time' => 'datetime',
        'is_distributed' => 'boolean',
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Getter cho tất cả status values
    public static function getStatusOptions()
    {
        return [
            self::STATUS_OPEN => 'Mở',
            self::STATUS_DISTRIBUTED => 'Đã phân phối',
            self::STATUS_ACTIVE => 'Đang xử lý',
            self::STATUS_RESOLVED => 'Đã giải quyết',
            self::STATUS_CLOSED => 'Đã đóng',
            self::STATUS_PENDING => 'Chờ xử lý'
        ];
    }

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationUser::class);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    // Helper methods
    public function isOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    // Thêm phương thức để tự động thêm thành viên chi nhánh khi được phân phối
    public function addBranchMembers()
    {
        if (!$this->branch_id) {
            return;
        }

        // Lấy tất cả nhân viên của chi nhánh
        $branchMembers = User::where('branch_id', $this->branch_id)
            ->whereIn('role', ['branch_admin', 'branch_staff'])
            ->get();

        foreach ($branchMembers as $member) {
            $this->participants()->firstOrCreate([
                'user_id' => $member->id,
                'user_type' => $member->role
            ]);
        }
    }
}
