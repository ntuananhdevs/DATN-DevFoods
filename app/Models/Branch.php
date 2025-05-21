<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'manager_user_id',
        'latitude',
        'longitude',
        'opening_hour',
        'closing_hour',
        'active',
        'balance',
        'rating',
        'reliability_score',
        'branch_code' // Thêm trường này vào
    ];

    /**
     * Lấy thông tin người quản lý chi nhánh
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }
}


