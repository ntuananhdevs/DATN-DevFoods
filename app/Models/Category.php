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
        $name = $this->name;
        
        // Xử lý các trường hợp đặc biệt
        $specialCases = [
            'Pizza' => 'PZ',
            'Burger' => 'BG',
            'Pasta' => 'PA',
            'Salad' => 'SL',
            'Drink' => 'DR',
            'Dessert' => 'DS',
            'Gà Rán' => 'GR',
            'Cơm' => 'CM',
            'Mì' => 'MI',
            'Đồ Uống' => 'DU',
        ];

        // Kiểm tra nếu tên nằm trong danh sách trường hợp đặc biệt
        foreach ($specialCases as $fullName => $shortName) {
            if (stripos($name, $fullName) !== false) {
                return $shortName;
            }
        }

        // Nếu không phải trường hợp đặc biệt, xử lý theo quy tắc chung
        $words = explode(' ', $name);
        if (count($words) === 1) {
            // Nếu chỉ có một từ, lấy 2 ký tự đầu
            return strtoupper(substr($name, 0, 2));
        } else {
            // Nếu có nhiều từ, lấy ký tự đầu của mỗi từ (tối đa 3 ký tự)
            $shortName = '';
            foreach ($words as $word) {
                $shortName .= strtoupper(substr($word, 0, 1));
                if (strlen($shortName) >= 3) break;
            }
            return $shortName;
        }
    }
}
