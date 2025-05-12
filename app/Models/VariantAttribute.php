<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Lấy giá trị biến thể
     */
    public function variantValues()
    {
        return $this->hasMany(VariantValue::class);
    }
    
    /**
     * Get the values for this variant attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(VariantValue::class);
    }
}