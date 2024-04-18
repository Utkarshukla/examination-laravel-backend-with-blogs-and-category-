<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //stduent //school
            $table->string('email'); //stduent //school
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('phone')->nullable(); //stduent //school
            $table->string('father')->nullable(); //stduent 
            $table->string('mother')->nullable(); //stduent
            $table->string('class')->nullable(); //stduent
            $table->string('gender')->nullable(); //school
            $table->date('dob')->nullable();//student 
            $table->tinyInteger('role')->default(5); 
            $table->string('city'); //stduent
            $table->string('district'); //stduent
            $table->string('state')->default('Andhra Pradesh');
            $table->string('pincode'); //stduent
            $table->string('school');//student //school
            $table->string('school-code')->nullable(); 
            $table->string('google_id')->nullable();
            $table->string('password')->nullable(); //student //school
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
