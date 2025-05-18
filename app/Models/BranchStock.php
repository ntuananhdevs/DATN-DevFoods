<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    /** @use HasFactory<\Database\Factories\BranchStockFactory> */
    use HasFactory;

    protected $table = 'branch_stock';
    protected $fillable = [
        'branch_id',
        'product_variant_id',
        'stock_quantity',
    ];

    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
