<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices compuestos para dashboard y consultas frecuentes
        Schema::table('diary_entries', function (Blueprint $table) {
            if (!Schema::hasIndex('diary_entries', 'idx_diary_user_favorite')) {
                $table->index(['user_id', 'is_favorite', 'deleted_at'], 'idx_diary_user_favorite');
            }
        });

        Schema::table('notes', function (Blueprint $table) {
            if (!Schema::hasIndex('notes', 'idx_notes_user_pinned')) {
                $table->index(['user_id', 'is_pinned', 'deleted_at'], 'idx_notes_user_pinned');
            }
        });

        Schema::table('goals', function (Blueprint $table) {
            if (!Schema::hasIndex('goals', 'idx_goals_user_active')) {
                $table->index(['user_id', 'is_completed', 'deleted_at'], 'idx_goals_user_active');
            }
        });

        Schema::table('habits', function (Blueprint $table) {
            if (!Schema::hasIndex('habits', 'idx_habits_user_active')) {
                $table->index(['user_id', 'is_active', 'deleted_at'], 'idx_habits_user_active');
            }
        });

        Schema::table('gratitudes', function (Blueprint $table) {
            if (!Schema::hasIndex('gratitudes', 'idx_gratitudes_user_date')) {
                $table->index(['user_id', 'date', 'deleted_at'], 'idx_gratitudes_user_date');
            }
        });

        Schema::table('todos', function (Blueprint $table) {
            if (!Schema::hasIndex('todos', 'idx_todos_user_completed_due')) {
                $table->index(['user_id', 'is_completed', 'due_date', 'priority'], 'idx_todos_user_completed_due');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasIndex('events', 'idx_events_user_start')) {
                $table->index(['user_id', 'start_date', 'deleted_at'], 'idx_events_user_start');
            }
        });

        // Full-text search para PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_diary_search ON diary_entries USING gin(to_tsvector('spanish', coalesce(title, '') || ' ' || coalesce(content, '')))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diary_entries', function (Blueprint $table) {
            $table->dropIndex('idx_diary_user_favorite');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex('idx_notes_user_pinned');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropIndex('idx_goals_user_active');
        });

        Schema::table('habits', function (Blueprint $table) {
            $table->dropIndex('idx_habits_user_active');
        });

        Schema::table('gratitudes', function (Blueprint $table) {
            $table->dropIndex('idx_gratitudes_user_date');
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->dropIndex('idx_todos_user_completed_due');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_user_start');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_diary_search');
        }
    }
};
