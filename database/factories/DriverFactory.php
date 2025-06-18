<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Driver;
use App\Models\User;
use App\Models\DriverApplication;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        // Tạo user mới
        $user = User::factory()->create();
        
        // Lấy một đơn đăng ký đã được phê duyệt ngẫu nhiên hoặc null
        $application = DriverApplication::where('status', 'approved')
                        ->inRandomOrder()
                        ->first();

        // Tạo tài xế mới
        return [
            'email' => $user->email,
            'password' => $user->password,
            'full_name' => $user->full_name,
            'phone_number' => $user->phone,
            'address' => $user->address ?? $this->faker->address(),
            'application_id' => $application?->id,
            'status' => 'active',
            'is_available' => $this->faker->boolean(80),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'cancellation_count' => $this->faker->numberBetween(0, 10),
            'reliability_score' => $this->faker->numberBetween(70, 100),
            'penalty_count' => $this->faker->numberBetween(0, 5),
            'auto_deposit_earnings' => $this->faker->boolean(30),
            // Thêm các field còn lại (nếu cần), đúng bảng drivers nhé!
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn () => [
            'is_available' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 'inactive',
        ]);
    }
}
