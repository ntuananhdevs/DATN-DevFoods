<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getShortNameAttribute()
    {
        // Lấy các từ đầu tiên của mỗi từ trong tên danh mục
        $words = explode(' ', $this->name);
        $shortName = '';
        foreach ($words as $word) {
            $shortName .= strtoupper(substr($word, 0, 1));
        }
        return $shortName;
    }
}
