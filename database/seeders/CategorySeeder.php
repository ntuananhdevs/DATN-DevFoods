<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pizza',
                'description' => 'Các loại bánh pizza đa dạng với nhiều hương vị',
                'status' => 1
            ],
            [
                'name' => 'Burger',
                'description' => 'Burger với nhiều lớp nhân thịt và rau củ tươi ngon',
                'status' => 1
            ],
            [
                'name' => 'Pasta',
                'description' => 'Các món mì Ý đậm đà hương vị',
                'status' => 1
            ],
            [
                'name' => 'Salad',
                'description' => 'Salad tươi ngon và bổ dưỡng',
                'status' => 1
            ],
            [
                'name' => 'Drink',
                'description' => 'Đồ uống đa dạng',
                'status' => 1
            ],
            [
                'name' => 'Dessert',
                'description' => 'Các món tráng miệng ngọt ngào',
                'status' => 1
            ],
            [
                'name' => 'Combo Set',
                'description' => 'Combo tiết kiệm',
                'status' => 1
            ],
            [
                'name' => 'Side Dish',
                'description' => 'Các món ăn kèm',
                'status' => 1
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
