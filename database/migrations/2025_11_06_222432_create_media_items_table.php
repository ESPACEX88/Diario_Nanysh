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
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['book', 'movie', 'series', 'music', 'podcast'])->default('book');
            $table->string('title');
            $table->string('author')->nullable(); // Autor, director, artista, etc.
            $table->text('description')->nullable();
            $table->enum('status', ['want', 'in_progress', 'completed'])->default('want');
            $table->integer('rating')->nullable(); // 1-5 estrellas
            $table->text('review')->nullable();
            $table->date('started_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('image_path')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
