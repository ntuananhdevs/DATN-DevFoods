<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'reason_type',
        'reason_detail',
    ];
}