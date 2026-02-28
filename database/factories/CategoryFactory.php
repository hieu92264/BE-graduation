<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

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
            'code' => fake()->unique()->bothify('CAT-###'),
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'sort_order' => fake()->numberBetween(0, 50),
            'remark' => fake()->optional()->sentence(),
            'user_name_created' => 'seeder',
            'user_name_updated' => 'seeder',
        ];
    }
}
