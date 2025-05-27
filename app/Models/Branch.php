<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Branch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_code',
        'name',
        'address',
        'phone',
        'email',
        'manager_user_id',
        'latitude',
        'longitude',
        'opening_hour',
        'closing_hour',
        'active',
        'balance',
        'rating',
        'reliability_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'reliability_score' => 'integer',
        'opening_hour' => 'datetime:H:i',
        'closing_hour' => 'datetime:H:i',
    ];

    /**
     * Get the images for the branch.
     */
    public function images(): HasMany
    {
        return $this->hasMany(BranchImage::class);
    }

    /**
     * Get the primary image for the branch.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(BranchImage::class)->where('is_primary', true);
    }

    /**
     * Get the manager of the branch.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(BranchStock::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            BranchStock::class,
            'branch_id', // Foreign key on branch_stocks table
            'id', // Foreign key on products table
            'id', // Local key on branches table
            'product_variant_id' // Local key on branch_stocks table
        );
    }
}