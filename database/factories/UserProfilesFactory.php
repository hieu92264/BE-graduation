<?php

namespace Database\Factories;

use App\Common\Enums\UserType;
use App\Models\User;
use App\Models\UserProfiles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserProfilesFactory extends Factory
{
    protected $model = UserProfiles::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isactive' => 'Y',
            'user_id' => User::factory(),
            'full_name' => $this->faker->name(),
            'phone_number' => $this->faker->optional()->phoneNumber(),
            'avatar_url' => $this->faker->optional()->imageUrl(200, 200, 'people'),
            'address' => $this->faker->optional()->address(),
            'zalo' => $this->faker->optional()->userName(),
            'facebook' => $this->faker->optional()->url(),
            'user_type' => fake()->randomElement(UserType::values()),
            'remark' => fake()->optional()->sentence(),
            'user_name_created' => 'seeder',
            'user_name_updated' => 'seeder'
        ];
    }
}
