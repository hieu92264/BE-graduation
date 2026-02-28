<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isactive' => 'Y',
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'last_login_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'last_login_ip' => fake()->optional()->ipv4(),
            'locale' => fake()->randomElement(['en', 'vi', 'cn']),
            'remark' => fake()->optional()->sentence(),
            'remember_token' => Str::random(10),
            'user_name_created' => 'Seeder',
            'user_name_updated' => 'Seeder',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
