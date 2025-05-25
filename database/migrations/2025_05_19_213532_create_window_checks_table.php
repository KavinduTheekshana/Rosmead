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
        Schema::create('window_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('room_number');
            $table->foreign('room_number')->references('room_number')->on('rooms');
            $table->integer('year');
            $table->integer('month');

            $table->boolean('fit_for_purpose')->default(false);
            $table->boolean('status')->default(false);
            $table->text('comment')->nullable();
            $table->text('action_taken')->nullable();
            $table->date('check_date')->default(now());

            // Add unique constraint for one reading per room per month
            $table->unique(['room_number', 'year', 'month']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('window_checks');
    }
};
