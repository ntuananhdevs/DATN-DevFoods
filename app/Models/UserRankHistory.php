<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRankHistory extends Model
{
    use HasFactory;
    protected $table = 'user_rank_history';
    
    protected $fillable = [
        'user_id', 'old_rank_id', 'new_rank_id', 'total_spending', 'total_orders', 'reason', 'changed_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'total_spending' => 'decimal:2',
        'total_orders' => 'integer',
        'changed_at' => 'datetime'
    ];

    // Mối quan hệ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldRank()
    {
        return $this->belongsTo(UserRank::class, 'old_rank_id');
    }

    public function newRank()
    {
        return $this->belongsTo(UserRank::class, 'new_rank_id');
    }
}