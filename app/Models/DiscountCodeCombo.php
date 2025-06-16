<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeCombo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount_code_id',
        'combo_id',
    ];

    /**
     * Get the discount code that owns the combo association.
     */
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get the combo associated with the discount code.
     */
    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
} 