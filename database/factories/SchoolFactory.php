<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_name'=>fake()->company(),
            'school_email'=>fake()->unique()->safeEmail(),
            'school_phone'=>fake()->unique()->numberBetween(4123456789,9999999999),
            'school_landmark'=>fake()->streetAddress(),
            'school_city'=>fake()->city(),
            'school_district'=>fake()->city(),
            'school_state'=>fake()->city(),
            'school_unique_code'=>fake()->unique()->text(),
            'author_id'=>1,
        ];
    }
}
