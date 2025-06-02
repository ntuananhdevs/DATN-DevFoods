<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'branch_id', 'status', 'is_distributed', 'distribution_time'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function participants()
    {
        return $this->hasMany(ConversationUser::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
