<?php

namespace Database\Factories;

use App\Common\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-10 days', '+10 days');
        $end = (clone $start);
        $end->modify('+' . fake()->numberBetween(1, 30) . ' days');

        $agreed = fake()->randomFloat(2, 500000, 15000000);
        $percent = fake()->randomFloat(2, 0, 10);
        $commission = round(((float)$agreed * (float)$percent) / 100.0, 2, PHP_ROUND_HALF_UP);

        return [
            'room_id' => Room::factory(),
            'tenant_user_id' => User::factory(),
            'landlord_user_id' => User::factory(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'agreed_price' => $agreed,
            'currency' => 'VND',
            'commission_percent' => $percent,
            'commission_amount' => $commission,
            'status' => fake()->randomElement(BookingStatus::values()),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
