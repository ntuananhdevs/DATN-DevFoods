<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    use HasFactory;

    protected $table = 'user_ranks';
    protected $fillable = [
        'name', 'slug', 'color', 'icon', 'min_spending', 'min_orders', 'discount_percentage', 'benefits', 'display_order', 'is_active'
    ];

    protected $casts = [
        'min_spending' => 'decimal:2',
        'min_orders' => 'integer',
        'discount_percentage' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean'
    ];

    // Mối quan hệ
    public function users()
    {
        return $this->hasMany(User::class, 'user_rank_id');
    }

    public function rankHistoryAsOld()
    {
        return $this->hasMany(UserRankHistory::class, 'old_rank_id');
    }

    public function rankHistoryAsNew()
    {
        return $this->hasMany(UserRankHistory::class, 'new_rank_id');
    }
}