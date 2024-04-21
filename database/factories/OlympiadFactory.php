<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Olympiad>
 */
class OlympiadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company,
            'description' => fake()->paragraph,
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->boolean,
            'registration_deadline' => fake()->date(),
            'author_id' => 1,
        ];
    }
}
