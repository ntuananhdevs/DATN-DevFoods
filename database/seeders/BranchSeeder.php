<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('vi_VN');
        
        // Tạo 10 chi nhánh với dữ liệu phong phú
        foreach (range(1, 10) as $index) {
            Branch::create([
                'name' => $faker->company,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'manager_user_id' => User::where('role_id', 2)->inRandomOrder()->first()->id,
                'latitude' => $faker->latitude(10, 11),
                'longitude' => $faker->longitude(106, 108),
                'opening_hour' => $faker->time('H:i', '07:00'),
                'closing_hour' => $faker->time('H:i', '22:00'),
                'active' => $faker->boolean(90),
                'balance' => $faker->randomFloat(2, 1000000, 50000000),
                'rating' => $faker->randomFloat(2, 3, 5),
                'reliability_score' => $faker->numberBetween(70, 100),
            ]);
        }
    }
}