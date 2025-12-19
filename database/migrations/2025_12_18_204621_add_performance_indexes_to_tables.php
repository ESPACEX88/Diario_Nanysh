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
        // Índices para diary_entries
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->index(['user_id', 'date'], 'idx_diary_user_date');
            $table->index(['user_id', 'is_favorite'], 'idx_diary_user_favorite');
            $table->index(['user_id', 'mood'], 'idx_diary_user_mood');
            $table->index('date', 'idx_diary_date');
        });

        // Índices para notes
        Schema::table('notes', function (Blueprint $table) {
            $table->index(['user_id', 'is_pinned'], 'idx_notes_user_pinned');
            $table->index(['user_id', 'created_at'], 'idx_notes_user_created');
        });

        // Índices para todos
        Schema::table('todos', function (Blueprint $table) {
            $table->index(['user_id', 'is_completed'], 'idx_todos_user_completed');
            $table->index(['user_id', 'due_date'], 'idx_todos_user_due');
            $table->index(['user_id', 'priority'], 'idx_todos_user_priority');
        });

        // Índices para goals
        Schema::table('goals', function (Blueprint $table) {
            $table->index(['user_id', 'is_completed'], 'idx_goals_user_completed');
            $table->index(['user_id', 'target_date'], 'idx_goals_user_target');
        });

        // Índices para habits
        Schema::table('habits', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'idx_habits_user_active');
        });

        // Índices para events
        Schema::table('events', function (Blueprint $table) {
            $table->index(['user_id', 'start_date'], 'idx_events_user_start');
            $table->index('start_date', 'idx_events_start');
        });

        // Índices para dreams
        Schema::table('dreams', function (Blueprint $table) {
            $table->index(['user_id', 'date'], 'idx_dreams_user_date');
            $table->index(['user_id', 'type'], 'idx_dreams_user_type');
        });

        // Índices para gratitudes
        Schema::table('gratitudes', function (Blueprint $table) {
            $table->index(['user_id', 'date'], 'idx_gratitudes_user_date');
        });

        // Índices para media_items
        Schema::table('media_items', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'idx_media_user_type');
            $table->index(['user_id', 'status'], 'idx_media_user_status');
        });

        // Índices para favorite_meals
        Schema::table('favorite_meals', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'idx_meals_user_type');
        });

        // Índices para photos
        Schema::table('photos', function (Blueprint $table) {
            $table->index(['user_id', 'taken_at'], 'idx_photos_user_taken');
        });

        // Índices para habit_logs
        Schema::table('habit_logs', function (Blueprint $table) {
            $table->index(['habit_id', 'completed_at'], 'idx_habit_logs_habit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->dropIndex('idx_diary_user_date');
            $table->dropIndex('idx_diary_user_favorite');
            $table->dropIndex('idx_diary_user_mood');
            $table->dropIndex('idx_diary_date');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('idx_notes_user_pinned');
            $table->dropIndex('idx_notes_user_created');
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->dropIndex('idx_todos_user_completed');
            $table->dropIndex('idx_todos_user_due');
            $table->dropIndex('idx_todos_user_priority');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex('idx_goals_user_completed');
            $table->dropIndex('idx_goals_user_target');
        });

        Schema::table('habits', function (Blueprint $table) {
            $table->dropIndex('idx_habits_user_active');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_user_start');
            $table->dropIndex('idx_events_start');
        });

        Schema::table('dreams', function (Blueprint $table) {
            $table->dropIndex('idx_dreams_user_date');
            $table->dropIndex('idx_dreams_user_type');
        });

        Schema::table('gratitudes', function (Blueprint $table) {
            $table->dropIndex('idx_gratitudes_user_date');
        });

        Schema::table('media_items', function (Blueprint $table) {
            $table->dropIndex('idx_media_user_type');
            $table->dropIndex('idx_media_user_status');
        });

        Schema::table('favorite_meals', function (Blueprint $table) {
            $table->dropIndex('idx_meals_user_type');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropIndex('idx_photos_user_taken');
        });

        Schema::table('habit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_habit_logs_habit_date');
        });
    }
};
