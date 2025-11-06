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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('album_id')->nullable();
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->morphs('photoable'); // For diary entries
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['user_id', 'album_id']);
            $table->index('taken_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
