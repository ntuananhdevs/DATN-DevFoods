<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserRank;

class UserRankFactory extends Factory
{
    protected $model = UserRank::class;

    public function definition(): array
    {
        static $ranks = [
            ['name' => 'Đồng', 'slug' => 'bronze', 'color' => '#CD7F32', 'min_spending' => 0, 'min_orders' => 0, 'discount_percentage' => 0],
            ['name' => 'Bạc', 'slug' => 'silver', 'color' => '#C0C0C0', 'min_spending' => 1000, 'min_orders' => 5, 'discount_percentage' => 5],
            ['name' => 'Vàng', 'slug' => 'gold', 'color' => '#FFD700', 'min_spending' => 5000, 'min_orders' => 10, 'discount_percentage' => 10],
            ['name' => 'Bạch Kim', 'slug' => 'platinum', 'color' => '#E5E4E2', 'min_spending' => 10000, 'min_orders' => 20, 'discount_percentage' => 15],
            ['name' => 'Kim Cương', 'slug' => 'diamond', 'color' => '#B9F2FF', 'min_spending' => 20000, 'min_orders' => 50, 'discount_percentage' => 20],
        ];

        static $index = 0;
        $rank = $ranks[$index % count($ranks)];
        $index++;

        // Kiểm tra xem slug đã tồn tại chưa
        if (UserRank::where('slug', $rank['slug'])->exists()) {
            return [];
        }

        return [
            'name' => $rank['name'],
            'slug' => $rank['slug'],
            'color' => $rank['color'],
            'icon' => "icons/{$rank['slug']}.png",
            'min_spending' => $rank['min_spending'],
            'min_orders' => $rank['min_orders'],
            'discount_percentage' => $rank['discount_percentage'],
            'benefits' => json_encode(['free_shipping' => $rank['min_spending'] >= 5000, 'priority_support' => $rank['min_spending'] >= 1000]),
            'display_order' => $index,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}