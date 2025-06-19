<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscountCode extends Model
{
    use HasFactory;
    protected $table = 'user_discount_codes';

    protected $fillable = [
        'user_id', 'discount_code_id', 'usage_count', 'status', 'assigned_at', 'first_used_at', 'last_used_at'
    ];

    public $timestamps = false;

    protected $casts = [
        'usage_count' => 'integer', 
        'status' => 'string',
        'assigned_at' => 'datetime',
        'first_used_at' => 'datetime',
        'last_used_at' => 'datetime'
    ];

    // Mối quan hệ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userdiscountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }
}