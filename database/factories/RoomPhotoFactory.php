<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomPhoto>
 */
class RoomPhotoFactory extends Factory
{
    protected $model = RoomPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'photo_url' => fake()->imageUrl(1200, 800, 'house'),
            'is_cover' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function cover(): static
    {
        return $this->state(fn() => ['is_cover' => true, 'sort_order' => 0]);
    }
}
