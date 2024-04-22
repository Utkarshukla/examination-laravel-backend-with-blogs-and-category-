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
            $table->string('name'); 
            $table->string('email'); 
            $table->unsignedBigInteger('aadhar_number')->nullable();
            $table->timestamp('email_verified_at')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('father')->nullable();  
            $table->string('mother')->nullable(); 
            $table->integer('class')->nullable(); 
            $table->string('gender')->nullable(); 
            $table->date('dob')->nullable();
            $table->tinyInteger('role')->default(5); //admin:1 school:2 approval:6 student:5
            $table->string('city')->nullable(); 
            $table->string('district');
            $table->string('state');
            $table->string('pincode'); 
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->string('google_id')->nullable();
            $table->string('password')->nullable(); 
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
