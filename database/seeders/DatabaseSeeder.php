<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        // \App\Models\User::factory()->create([
        //     'name' => 'Super Admin',
        //     'email' => 'admin@gmail.com',
        //     'role'=>1,
        //     'district'=>"Vijaywada",
        //     'state'=>"Andhra Pradesh",
        //     'pincode'=>'226020',
        //     'school_id'=>1,
        //     'password'=> Hash::make('Jass@007')
        // ]);
            \App\Models\School::factory(10)->create();
            \App\Models\User::factory(100)->create();
            \App\Models\Olympiad::factory(100)->create();
            \App\Models\TicketCount::factory(100)->create();
            \App\Models\Subject::factory(400)->create();
            \App\Models\Participate::factory(10000)->create();
            \App\Models\ParticipantSubject::factory(40000)->create();
            
        
    }
}
