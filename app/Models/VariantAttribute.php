<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $table = 'variant_attributes';
    protected $fillable = ['name'];

    public function values()
    {
        return $this->hasMany(VariantValue::class, 'variant_attribute_id');
    }
}