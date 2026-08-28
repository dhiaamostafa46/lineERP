<?php

namespace Database\Factories;

use App\Models\Vehicles\DriverCompanyReference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverCompanyReference>
 */
class DriverCompanyReferenceFactory extends Factory
{
    protected $model = DriverCompanyReference::class;

    public function definition(): array
    {
        return [
            'driver_id' => 1,
            'company_id' => 1,
            'ref_no' => fake()->unique()->numerify('REF-#####'),
            'status' => DriverCompanyReference::STATUS_ACTIVE,
            'started_at' => now(),
            'ended_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => DriverCompanyReference::STATUS_COMPLETED,
            'ended_at' => now(),
        ]);
    }
}
