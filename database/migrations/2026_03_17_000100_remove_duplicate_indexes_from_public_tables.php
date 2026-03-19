<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS public.diary_entries_user_id_date_index');
        DB::statement('DROP INDEX IF EXISTS public.diary_entries_user_id_is_favorite_index');
        DB::statement('DROP INDEX IF EXISTS public.dreams_user_id_date_index');
        DB::statement('DROP INDEX IF EXISTS public.events_user_id_start_date_index');
        DB::statement('DROP INDEX IF EXISTS public.favorite_meals_user_id_type_index');
        DB::statement('DROP INDEX IF EXISTS public.goals_user_id_is_completed_index');
        DB::statement('DROP INDEX IF EXISTS public.gratitudes_user_id_date_index');
        DB::statement('DROP INDEX IF EXISTS public.idx_gratitudes_user_date');
        DB::statement('DROP INDEX IF EXISTS public.habits_user_id_is_active_index');
        DB::statement('DROP INDEX IF EXISTS public.todos_user_id_due_date_index');
        DB::statement('DROP INDEX IF EXISTS public.todos_user_id_is_completed_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE INDEX IF NOT EXISTS diary_entries_user_id_date_index ON public.diary_entries (user_id, date)');
        DB::statement('CREATE INDEX IF NOT EXISTS diary_entries_user_id_is_favorite_index ON public.diary_entries (user_id, is_favorite)');
        DB::statement('CREATE INDEX IF NOT EXISTS dreams_user_id_date_index ON public.dreams (user_id, date)');
        DB::statement('CREATE INDEX IF NOT EXISTS events_user_id_start_date_index ON public.events (user_id, start_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS favorite_meals_user_id_type_index ON public.favorite_meals (user_id, type)');
        DB::statement('CREATE INDEX IF NOT EXISTS goals_user_id_is_completed_index ON public.goals (user_id, is_completed)');
        DB::statement('CREATE INDEX IF NOT EXISTS gratitudes_user_id_date_index ON public.gratitudes (user_id, date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_gratitudes_user_date ON public.gratitudes (user_id, date)');
        DB::statement('CREATE INDEX IF NOT EXISTS habits_user_id_is_active_index ON public.habits (user_id, is_active)');
        DB::statement('CREATE INDEX IF NOT EXISTS todos_user_id_due_date_index ON public.todos (user_id, due_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS todos_user_id_is_completed_index ON public.todos (user_id, is_completed)');
    }
};
