<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Driver;
use App\Models\User;
use App\Models\DriverApplication;
use App\Models\Role;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        // Tạo user mới (KHÔNG gán role_id)
        $user = User::factory()->create();
        
        // Lấy một đơn đăng ký đã được phê duyệt ngẫu nhiên hoặc null
        $application = DriverApplication::where('status', 'approved')
                        ->inRandomOrder()
                        ->first();
        
        return [
            'email' => $user->email,
            'password' => $user->password,
            'full_name' => $user->full_name,
            'phone_number' => $user->phone,
            'application_id' => $application?->id,
            'license_number' => $this->faker->unique()->numerify('DL##########'),
            'vehicle_type' => $application?->vehicle_type ?? $this->faker->randomElement(['motorcycle', 'car', 'bicycle']),
            'vehicle_registration' => $application?->license_plate ?? $this->faker->bothify('??-###-##'),
            'vehicle_color' => $application?->vehicle_color ?? $this->faker->colorName(),
            'status' => 'active',
            'is_available' => $this->faker->boolean(80),
            'current_latitude' => $this->faker->latitude(),
            'current_longitude' => $this->faker->longitude(),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'cancellation_count' => $this->faker->numberBetween(0, 10),
            'reliability_score' => $this->faker->numberBetween(70, 100),
            'penalty_count' => $this->faker->numberBetween(0, 5),
            'auto_deposit_earnings' => $this->faker->boolean(30),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_available' => false,
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }
}