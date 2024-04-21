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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('test@007'),
            'aadhar_number'=>fake()->unique()->numberBetween(000000000000,999999999999),
            'phone'=>fake()->numberBetween(4123456789,9999999999),
            'father'=>fake()->firstNameMale(),
            'mother'=>fake()->firstNameFemale(),
            'class'=>fake()->numberBetween(3,10),
            'dob'=>fake()->date('Y-m-d','now'),
            'city'=>fake()->streetName(),
            'district'=>fake()->city(),
            'pincode'=>fake()->numberBetween(000000,999999),
            'school_id'=>fake()->numberBetween(2,10),
            'state'=>"Andra Pradesh",
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
