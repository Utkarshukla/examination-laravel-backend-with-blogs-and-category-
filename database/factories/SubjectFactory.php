<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'olympiad_id' =>fake()->numberBetween(1, 100) ,
        'subject' => fake()->word,
        'subject_class' =>10,// fake()->numberBetween(3, 10),
        'subject_fee' => fake()->numberBetween(150, 100),
        'subject_marks' => fake()->numberBetween(100, 100)
        ];
    }
}
