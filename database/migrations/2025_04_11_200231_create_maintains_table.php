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
        Schema::create('maintains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('room_number');
            $table->string('resident_name')->nullable();
            $table->string('bed_type')->nullable();
            $table->boolean('has_bed_rails')->default(false);
            $table->boolean('has_bed_rail_covers')->default(false);
            $table->enum('mattress_type', ['Normal Mattress', 'Air Mattress'])->default('Normal Mattress');
            $table->string('air_mattress_machine_type')->nullable();
            $table->boolean('has_sensor_mat')->default(false);
            $table->boolean('has_ceiling_light')->default(false);
            $table->boolean('has_ceiling_fan')->default(false);
            $table->boolean('has_wall_light')->default(false);
            $table->boolean('has_bathroom_light')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('has_door_lock')->default(false);
            $table->string('door_lock_pin')->nullable();
            $table->boolean('has_tv')->default(false);
            $table->string('tv_model')->nullable();
            $table->boolean('has_tv_remote')->default(false);
            $table->enum('tv_place', ['Wall fixed', 'Table'])->nullable();
            $table->json('comments')->nullable()->comment('Comments with date stamps');
            $table->json('window_lock_checked')->nullable()->comment('Window Lock Checked with date stamps');
            $table->json('fire_door_guard_checked')->nullable()->comment('Fire Door Guard Checked with date stamps');
            $table->json('fire_door_guard_battery_replaced')->nullable()->comment('Fire Door Guard Battery Replaced date stamps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintains');
    }
};
