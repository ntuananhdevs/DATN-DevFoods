<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionBranch extends Model
{
    use HasFactory;
    protected $table = 'promotion_branches';
    protected $fillable = ['promotion_program_id', 'branch_id'];

    // Mối quan hệ
    public function promotionProgram()
    {
        return $this->belongsTo(PromotionProgram::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
