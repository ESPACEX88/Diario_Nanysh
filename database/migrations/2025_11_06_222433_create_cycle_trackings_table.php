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
        Schema::create('cycle_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->enum('phase', ['menstrual', 'follicular', 'ovulation', 'luteal'])->nullable();
            $table->integer('cycle_day')->nullable(); // Día del ciclo (1-35)
            $table->json('symptoms')->nullable(); // Síntomas: ['cramps', 'bloating', 'mood_swings']
            $table->string('mood')->nullable();
            $table->integer('flow_level')->nullable(); // 1-5
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_trackings');
    }
};
