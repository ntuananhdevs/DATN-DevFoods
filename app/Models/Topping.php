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
        'sku',
        'name',
        'price',
        'active',
        'image',
        'description',
        'created_by',
        'updated_by'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_toppings')
            ->withTimestamps();
    }
    
    /**
     * Get all stock entries for this topping across branches
     */
    public function toppingStocks(): HasMany
    {
        return $this->hasMany(ToppingStock::class);
    }
    
    /**
     * Get all stock entries for this topping across branches (alias for compatibility)
     */
    public function stocks(): HasMany
    {
        return $this->toppingStocks();
    }
    
    /**
     * Get stock quantity at a specific branch
     * 
     * @param int $branchId
     * @return int
     */
    public function getStockAtBranch($branchId): int
    {
        return $this->toppingStocks()
            ->where('branch_id', $branchId)
            ->value('stock_quantity') ?? 0;
    }
    
    /**
     * Người tạo topping
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Người cập nhật topping
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}