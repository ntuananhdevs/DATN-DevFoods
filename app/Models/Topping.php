<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'active'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_toppings')
            ->withTimestamps();
    }
} 