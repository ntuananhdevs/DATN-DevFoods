<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'message',
        'sender_type',
        'receiver_type',
        'branch_id',
        'status',
        'read_at',
        'sent_at',
        'attachment',
        'attachment_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_system_message' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->sent_at = now();
        });
    }

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include system messages.
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('is_system_message', true);
    }

    /**
     * Scope a query to only include user messages.
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_system_message', false);
    }

    /**
     * Scope a query to only include messages with attachments.
     */
    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachment');
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead()
    {
        $this->is_read = true;
        return $this->save();
    }

    /**
     * Check if the message has an attachment.
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrl()
    {
        if ($this->hasAttachment()) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }

    // Phương thức để xác định người nhận tin nhắn
    public function getReceivers()
    {
        if ($this->receiver_type === 'branch_admin' || $this->receiver_type === 'branch_staff') {
            // Nếu người nhận là chi nhánh, lấy tất cả thành viên của chi nhánh đó
            return User::where('branch_id', $this->branch_id)
                ->whereIn('role', ['branch_admin', 'branch_staff'])
                ->get();
        }

        // Nếu người nhận là khách hàng, lấy thông tin khách hàng
        return User::where('id', $this->conversation->customer_id)->get();
    }
}
