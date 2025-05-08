<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'base_price',
        'stock',
        'image',
        'preparation_time',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
