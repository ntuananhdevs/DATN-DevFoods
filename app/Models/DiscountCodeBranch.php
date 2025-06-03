<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCodeBranch extends Model
{
    use HasFactory;
    protected $table = 'discount_code_branches';
    protected $fillable = ['discount_code_id', 'branch_id'];

    // Mối quan hệ
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
