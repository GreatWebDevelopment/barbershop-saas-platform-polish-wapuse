<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional(0.3)->sentence(),
            'loyalty_points' => fake()->numberBetween(0, 500),
            'visit_count' => fake()->numberBetween(1, 30),
            'last_visit' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
