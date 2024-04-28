<?php

namespace Database\Factories;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Participate;

class ParticipateFactory extends Factory
{
    protected $model = Participate::class;

    public function definition()
    {
        // $faker = FakerFactory::create();

        return [
            'user_id' => fake()->numberBetween(1, 100),
            'school_id' => null,
            'olympiad_id' => fake()->numberBetween(1, 100),
            'aadhar_number' =>fake()->numberBetween(123456789012, 987654321012),
            'class' => fake()->numberBetween(1, 10),
            'total_amount' => 150,
            'total_ammount_locked' => 1,
            'payment_id' => null,
            'payment_type' => null,
            'isfullPaid' => 1,
            'hall_ticket_no' => null,
            'ticket_send' => null,
            'total_marks' => 100,
            'obtain_marks' => 90,
            'certificate_url' => null,
            'certificate_downloads' => null,
            'created_by' => 0,
        ];
    }
}
