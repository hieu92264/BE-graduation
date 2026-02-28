<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'user_id' => User::factory(),
            'content' => fake()->paragraph(),
            'rating' => fake()->optional()->numberBetween(1, 5),
            'status' => fake()->randomElement(['visible', 'hidden']),
        ];
    }
}
