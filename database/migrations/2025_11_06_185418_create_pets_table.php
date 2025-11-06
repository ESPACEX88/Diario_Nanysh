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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('name')->default('Snoopy');
            $table->integer('level')->default(1);
            $table->integer('experience')->default(0);
            $table->integer('happiness')->default(100); // 0-100
            $table->integer('hunger')->default(100); // 0-100
            $table->integer('energy')->default(100); // 0-100
            $table->integer('health')->default(100); // 0-100
            $table->integer('coins')->default(0);
            $table->timestamp('last_fed_at')->nullable();
            $table->timestamp('last_played_at')->nullable();
            $table->timestamp('last_cared_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
