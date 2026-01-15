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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('workout_date');
            $table->string('routine_name');
            $table->text('exercises')->nullable(); // JSON con los ejercicios realizados
            $table->integer('duration_minutes')->nullable(); // Duración en minutos
            $table->text('notes')->nullable(); // Notas adicionales
            $table->enum('intensity', ['light', 'moderate', 'intense'])->default('moderate');
            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index(['user_id', 'workout_date']);
            $table->unique(['user_id', 'workout_date']); // Solo un entrenamiento por día
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
