<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParticipantSubject>
 */
class ParticipantSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_id' =>fake()->numberBetween(1, 10000),
            'student_id' => fake()->numberBetween(1, 100),
            'subject_id' => fake()->numberBetween(1, 400),
            'obtain_marks' => 90,
        ];
    }
}
