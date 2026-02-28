<?php

namespace Database\Factories;

use App\Common\Enums\BookingStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\PostType;
use App\Models\Room;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        return [
            'isactive' => 'Y',
            'owner_user_id' => User::factory(),
            'category_id' => Category::factory(),
            'post_type_id' => PostType::factory(),
            'city_id' => City::factory(),
            'district_id' => District::factory(),
            'ward_id' => Ward::factory(),

            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 999999),
            'address' => fake()->address(),
            'price' => fake()->randomFloat(2, 500000, 15000000),
            'area' => fake()->randomFloat(2, 12, 120),
            'description' => fake()->paragraph(4),
            'booking_status' => fake()->randomElement(BookingStatus::values()),
        ];
    }
}
