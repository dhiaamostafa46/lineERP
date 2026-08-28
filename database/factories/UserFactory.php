<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create('ar_JO');

        return [
            'name'         => $faker->name,
            'username'     => $faker->userName,
            'country_id'   => 1,
            'city_id'      => 1,
            'specialty_id' => 1,
            'email'        => $faker->unique()->safeEmail,
            'password'     => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',   // password
            'image'        => 'avtr.jpg',
        ];
    }
}
