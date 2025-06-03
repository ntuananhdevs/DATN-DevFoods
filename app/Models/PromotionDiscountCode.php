<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionDiscountCode extends Model
{
    use HasFactory;

    protected $fillable = ['promotion_program_id', 'discount_code_id'];

    public function promotionProgram()
    {
        return $this->belongsTo(PromotionProgram::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }
}