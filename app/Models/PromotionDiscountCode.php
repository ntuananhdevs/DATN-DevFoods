<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDiscountCode extends Model
{
    protected $table = 'promotion_discount_codes';

    protected $fillable = ['promotion_program_id', 'discount_code_id'];

    // Mối quan hệ
    public function promotionProgram()
    {
        return $this->belongsTo(PromotionProgram::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }
}
