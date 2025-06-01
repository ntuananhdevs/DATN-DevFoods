<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToppingStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'topping_id',
        'stock_quantity',
    ];

    /**
     * Get the branch that this stock belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the topping that this stock entry is for
     */
    public function topping()
    {
        return $this->belongsTo(Topping::class);
    }
} 