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
        Schema::create('recipe_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('recipe'); // Instrucciones completas
            $table->enum('type', ['breakfast', 'lunch', 'dinner', 'snack', 'dessert', 'drink'])->default('lunch');
            $table->json('ingredients')->nullable();
            $table->integer('prep_time')->nullable(); // Minutos
            $table->integer('cook_time')->nullable(); // Minutos
            $table->integer('servings')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('category')->nullable(); // saludable, rápida, postre, vegana, etc.
            $table->integer('calories')->nullable();
            $table->boolean('is_predefined')->default(true); // Si es del catálogo o personalizada
            $table->timestamps();
            
            $table->index(['type', 'difficulty']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_catalog');
    }
};
