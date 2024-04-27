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
        Schema::create('participant_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('participant_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('subject_id');
                $table->integer('obtain_marks')->nullable();
                $table->foreign('participant_id')->references('id')->on('participates');
                $table->foreign('student_id')->references('id')->on('users');
                $table->foreign('subject_id')->references('id')->on('subjects');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_subjects');
    }
};
