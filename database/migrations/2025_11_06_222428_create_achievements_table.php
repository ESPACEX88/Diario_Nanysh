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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Código único del logro
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable(); // Emoji o nombre de icono
            $table->string('color', 7)->default('#EC4899');
            $table->integer('points')->default(0); // Puntos que otorga
            $table->string('type'); // diary, habit, pet, todo, etc.
            $table->integer('requirement_value')->nullable(); // Valor requerido (ej: 7 días seguidos)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
