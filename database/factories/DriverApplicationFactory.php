<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DriverApplication;

class DriverApplicationFactory extends Factory
{
    protected $model = DriverApplication::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => '+84 ' . $this->faker->unique()->numberBetween(100000000, 999999999),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'id_card_number' => $this->faker->unique()->numerify('##########'),
            'id_card_issue_date' => $this->faker->date('Y-m-d', '-5 years'),
            'id_card_issue_place' => $this->faker->city(),
            'address' => $this->faker->address(),
            // 'city' => $this->faker->city(),
            // 'district' => $this->faker->word(),
            'vehicle_type' => $this->faker->randomElement(['motorcycle', 'car', 'bicycle']),
            'vehicle_model' => $this->faker->word(),
            'vehicle_color' => $this->faker->colorName(),
            'license_plate' => $this->faker->unique()->bothify('??-###-##'),
            'driver_license_number' => $this->faker->unique()->numerify('DL##########'),
            'id_card_front_image' => 'images/id_cards/front_default.jpg',
            'id_card_back_image' => 'images/id_cards/back_default.jpg',
            'driver_license_image' => 'images/licenses/default.jpg',
            'profile_image' => 'images/profiles/default.jpg',
            'vehicle_registration_image' => 'images/vehicles/default.jpg',
            'bank_name' => $this->faker->company(),
            'bank_account_number' => $this->faker->numerify('##############'),
            'bank_account_name' => $this->faker->name(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => '+84 ' . $this->faker->unique()->numberBetween(100000000, 999999999),
            'emergency_contact_relationship' => $this->faker->randomElement(['parent', 'spouse', 'sibling', 'friend']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }
}