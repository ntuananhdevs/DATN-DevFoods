<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverLocation;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 tài xế
        $drivers = Driver::factory(8)->create();
        $drivers = $drivers->concat(Driver::factory(2)->inactive()->create());

        // Tạo document và location cho mỗi tài xế
        foreach ($drivers as $driver) {
            DriverDocument::create([
                'driver_id' => $driver->id,
                'license_number' => fake()->unique()->numerify('DL##########'),
                'license_class' => fake()->randomElement(['A1','A2','B1','B2','C']),
                'license_expiry' => fake()->dateTimeBetween('+1 year', '+10 years'),
                'license_front' => 'images/licenses/front_default.jpg',
                'license_back' => 'images/licenses/back_default.jpg',
                'id_card_front' => 'images/id_cards/front_default.jpg',
                'id_card_back' => 'images/id_cards/back_default.jpg',
                'vehicle_type' => fake()->randomElement(['motorbike','car','truck']),
                'vehicle_registration' => fake()->bothify('??-###-##'),
                'vehicle_color' => fake()->colorName(),
                'license_plate' => fake()->bothify('??-###-##'),
            ]);
            DriverLocation::create([
                'driver_id' => $driver->id,
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
                'updated_at' => now(),
            ]);
        }
    }
}
