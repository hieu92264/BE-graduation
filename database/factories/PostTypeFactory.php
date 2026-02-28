<?php

namespace Database\Factories;

use App\Models\PostType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostType>
 */
class PostTypeFactory extends Factory
{
    protected $model = PostType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'isactive' => 'Y',
            'code' => fake()->unique()->bothify('POST-###'),
            'name' => Str::title($name),
            'priority' => fake()->numberBetween(0, 10),
            'default_days' => fake()->numberBetween(7, 60),
            'price' => fake()->randomFloat(2, 0, 5000000),
            'remark' => fake()->optional()->sentence(),
            'user_name_created' => 'seeder',
            'user_name_updated' => 'seeder',
        ];
    }
}
