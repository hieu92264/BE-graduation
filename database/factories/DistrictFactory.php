<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\District>
 */
class DistrictFactory extends Factory
{
    protected $model = District::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isactive' => 'Y',
            'code' => fake()->unique()->bothify('D##'),
            'name' => fake()->unique()->streetName(),
            'city_id' => City::factory(),
            'sort_order' => fake()->numberBetween(0, 200),
        ];
    }
}
