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
        Schema::create('participates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('olympiad_id');
            $table->unsignedBigInteger('aadhar_number')->nullable();
            $table->integer('class');
            $table->integer('total_amount')->nullable();
            $table->boolean('total_ammount_locked')->nullable()->default(false);
            $table->string('payment_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->boolean('isfullPaid')->nullable()->default(false);
            $table->string('hall_ticket_no')->nullable();
            $table->boolean('ticket_send')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('obtain_marks')->nullable();
            $table->string('certificate_url')->nullable();
            $table->integer('certificate_downloads')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('olympiad_id')->references('id')->on('olympiads');
            $table->foreign('school_id')->references('id')->on('schools');
            $table->unsignedBigInteger('created_by')->default(0);
            // $table->foreign('payment_id')->references('id')->on('payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participates');
    }
};
