<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
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

        // Tạo tên chi nhánh duy nhất
        $city = $this->faker->city();
        $district = $this->faker->streetName();
        $name = "Chi nhánh {$city} - {$district}";
        
        return [
            'name' => $name,
            'branch_code' => 'BR' . $this->faker->unique()->numberBetween(1000, 9999),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'manager_user_id' => $this->faker->randomElement($managerIds),
            'latitude' => $this->faker->latitude(8.0, 9.0),
            'longitude' => $this->faker->longitude(105.0, 107.0),
            'opening_hour' => $openingHour,
            'closing_hour' => $closingHour,
            'active' => $this->faker->boolean(80),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'reliability_score' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}