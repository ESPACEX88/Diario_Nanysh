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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('category')->nullable();
            $table->string('color', 7)->default('#fbbf24'); // Hex color
            $table->boolean('is_pinned')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('has_checklist')->default(false);
            $table->json('checklist_items')->nullable(); // Array of {text, completed}
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['user_id', 'is_pinned', 'order']);
            // FullText index for MySQL (requires MyISAM or InnoDB with MySQL 5.6+)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
