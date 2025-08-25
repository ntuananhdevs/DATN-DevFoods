<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'reply',
        'reply_date',
        'is_official',
        'is_hidden',
        'helpful_count',
        'report_count',
    ];

    protected $casts = [
        'reply_date' => 'datetime',
        'is_official' => 'boolean',
        'is_hidden' => 'boolean',
        'helpful_count' => 'integer',
        'report_count' => 'integer',
    ];

    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}