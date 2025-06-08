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
        Schema::create('fire_door_guard_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintain_id')->constrained('maintains')->onDelete('cascade');
            $table->date('checked_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add index for better performance
            $table->index(['maintain_id', 'checked_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_door_guard_checks');
    }
};