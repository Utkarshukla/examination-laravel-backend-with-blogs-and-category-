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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('olympiad_id');
            $table->string('subject');
            $table->string('subject_class');
            $table->integer('subject_fee');
            $table->integer('subject_marks');
            $table->foreign('olympiad_id')->references('id')->on('olympiads');
            $table->index('subject');
            $table->index('subject_class');
            $table->index('subject_fee');
            $table->index('subject_marks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
