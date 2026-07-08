<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createIndexIfMissing('diary_entries', 'idx_diary_user_favorite', ['user_id', 'is_favorite', 'deleted_at']);
        $this->createIndexIfMissing('notes', 'idx_notes_user_pinned', ['user_id', 'is_pinned', 'deleted_at']);
        $this->createIndexIfMissing('goals', 'idx_goals_user_active', ['user_id', 'is_completed', 'deleted_at']);
        $this->createIndexIfMissing('habits', 'idx_habits_user_active', ['user_id', 'is_active', 'deleted_at']);
        $this->createIndexIfMissing('gratitudes', 'idx_gratitudes_user_date', ['user_id', 'date']);
        $this->createIndexIfMissing('todos', 'idx_todos_user_completed_due', ['user_id', 'is_completed', 'due_date', 'priority']);
        $this->createIndexIfMissing('events', 'idx_events_user_start', ['user_id', 'start_date']);

        if (DB::getDriverName() === 'pgsql' && Schema::hasTable('diary_entries')) {
            DB::statement("CREATE INDEX IF NOT EXISTS idx_diary_search ON diary_entries USING gin(to_tsvector('spanish', coalesce(title, '') || ' ' || coalesce(content, '')))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('diary_entries', 'idx_diary_user_favorite');
        $this->dropIndexIfExists('notes', 'idx_notes_user_pinned');
        $this->dropIndexIfExists('goals', 'idx_goals_user_active');
        $this->dropIndexIfExists('habits', 'idx_habits_user_active');
        $this->dropIndexIfExists('gratitudes', 'idx_gratitudes_user_date');
        $this->dropIndexIfExists('todos', 'idx_todos_user_completed_due');
        $this->dropIndexIfExists('events', 'idx_events_user_start');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_diary_search');
        }
    }

    private function createIndexIfMissing(string $table, string $indexName, array $columns): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $indexName)) {
            return;
        }

        $columns = $this->resolveColumns($table, $columns);

        if ($columns === []) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasIndex($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    /**
     * @param  string[]  $columns
     * @return string[]
     */
    private function resolveColumns(string $table, array $columns): array
    {
        return array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn($table, $column)
        ));
    }
};
