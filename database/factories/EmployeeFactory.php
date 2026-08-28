<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeFactory extends Factory
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
            'user_id'            => 1,
            'full_name'          => $faker->name,
            'username'           => $faker->userName,
            'phone'              => $faker->phoneNumber,
            'email'              => $faker->email,
            'dob'                => $faker->date,
            'address'            => $faker->address,
            'national_address'   => $faker->address,
            'religion'           => 'Muslim',
            'gender'             => $faker->numberBetween(1, 2),
            'marital_status'     => $faker->numberBetween(1, 5),
            'number_of_children' => $faker->numberBetween(0, 10),
            'nationality'        => 'Pakistani',
        ];
    }
}
