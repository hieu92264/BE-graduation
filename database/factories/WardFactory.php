<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ward>
 */
class WardFactory extends Factory
{
    protected $model = Ward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isactive' => 'Y',
            'code' => fake()->unique()->bothify('W##'),
            'name' => fake()->unique()->streetName(),
            'district_id' => District::factory(),
            'sort_order' => fake()->numberBetween(0, 300),
        ];
    }
}
