<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the payments for this payment method.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
} 