<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'active',
        'image',
        'description'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_toppings')
            ->withTimestamps();
    }
    
    /**
     * Get all stock entries for this topping across branches
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ToppingStock::class);
    }
    
    /**
     * Get stock quantity at a specific branch
     * 
     * @param int $branchId
     * @return int
     */
    public function getStockAtBranch($branchId): int
    {
        return $this->stocks()
            ->where('branch_id', $branchId)
            ->value('stock_quantity') ?? 0;
    }
} 