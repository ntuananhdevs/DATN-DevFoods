<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        $openingHour = $this->faker->time('H:i', '10:00');
        $closingHour = $this->faker->time('H:i', '22:00');
        
        // Đảm bảo closing_hour luôn sau opening_hour
        while (strtotime($closingHour) <= strtotime($openingHour)) {
            $closingHour = $this->faker->time('H:i', '22:00');
        }
        
        // Lấy ID của user có role là manager
        $managerIds = User::whereHas('roles', function($query) {
            $query->where('name', 'manager');
        })->pluck('id')->toArray();
        
        // Nếu không có manager nào, tạo một user mới
        if (empty($managerIds)) {
            $manager = User::factory()->create();
            $managerIds = [$manager->id];
        }
        
        return [
            'name' => 'Chi nhánh ' . $this->faker->city,
            'address' => $this->faker->address,
            'phone' => $this->faker->numerify('0##########'),
            'email' => $this->faker->unique()->safeEmail,
            'manager_user_id' => $this->faker->randomElement($managerIds),
            'latitude' => $this->faker->latitude(8, 23),
            'longitude' => $this->faker->longitude(102, 109),
            'opening_hour' => $openingHour,
            'closing_hour' => $closingHour,
            'active' => $this->faker->boolean(80), // 80% là active
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'reliability_score' => $this->faker->numberBetween(70, 100),
        ];
    }
}