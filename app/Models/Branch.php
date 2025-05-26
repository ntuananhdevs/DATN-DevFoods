<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_hour' => 'datetime:H:i',
        'closing_hour' => 'datetime:H:i',
        'active' => 'boolean',
        'balance' => 'decimal:2',
        'rating' => 'decimal:2',
        'reliability_score' => 'integer',
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
}