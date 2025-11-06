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
        Schema::create('favorite_meals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('recipe')->nullable(); // Receta
            $table->string('image_path')->nullable();
            $table->enum('type', ['breakfast', 'lunch', 'dinner', 'snack', 'dessert', 'drink'])->default('lunch');
            $table->json('ingredients')->nullable();
            $table->integer('prep_time')->nullable(); // Minutos
            $table->integer('cook_time')->nullable(); // Minutos
            $table->integer('servings')->nullable();
            $table->integer('rating')->nullable(); // 1-5 estrellas
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_meals');
    }
};
