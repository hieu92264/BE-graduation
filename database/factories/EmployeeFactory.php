<?php

namespace Database\Factories;

use App\Common\Enums\WorkStatus;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

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
            'employee_code' => fake()->unique()->bothify('EMP-####'),
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'dob' => $this->faker->date('Y-m-d', '-18 years'),
            'avatar_url' => $this->faker->imageUrl(200, 200, 'people'),
            'status' => $this->faker->randomElement(array_column(WorkStatus::cases(), 'value')),
            'join_date' => $this->faker->dateTimeBetween('-3 years', 'now')?->format('Y-m-d'),
            'terminate_date' => null,
            'remark' => $this->faker->optional()->sentence(),
            'user_name_created' => 'Seeder',
            'user_name_updated' => 'Seeder',
        ];
    }
}
