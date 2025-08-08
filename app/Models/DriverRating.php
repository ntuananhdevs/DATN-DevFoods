<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'order_id',
        'rating',
        'comment',
        'is_anonymous',
        'rated_at',
        'is_flagged',
        'admin_notes'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
        'rated_at' => 'datetime',
        'is_flagged' => 'boolean',
    ];

    /**
     * Get the user who submitted the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the driver being rated.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the order associated with this rating.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}