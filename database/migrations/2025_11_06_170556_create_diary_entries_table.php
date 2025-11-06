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
        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('content');
            $table->string('mood', 10); // Emoji mood
            $table->date('date');
            $table->boolean('is_favorite')->default(false);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'is_favorite']);
            // FullText index for MySQL (requires MyISAM or InnoDB with MySQL 5.6+)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
