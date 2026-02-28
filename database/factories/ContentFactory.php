<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        return [
            'isactive' => 'Y',
            'author_user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'thumbnail_url' => fake()->optional()->imageUrl(1200, 630, 'business'),
            'content' => fake()->paragraphs(6, true),
            'status' => fake()->randomElement(['draft', 'published', 'hidden']),
            'published_at' => fake()->optional()->dateTimeBetween('-60 days', 'now'),
            'remark' => fake()->optional()->sentence(),
            'user_name_created' => 'seeder',
            'user_name_updated' => 'seeder',
        ];
    }
}
