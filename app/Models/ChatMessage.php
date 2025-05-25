<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'branch_id',
        'message',
        'attachment',
        'attachment_type',
        'sent_at',
        'status',
        'read_at',
        'is_deleted',
        'is_system_message',
        'related_order_id',
        'sender_type',
        'receiver_type',
    ];
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
