<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // Logros de Diario
            [
                'code' => 'first_entry',
                'name' => 'Primer Paso',
                'description' => 'Escribe tu primera entrada en el diario',
                'icon' => '📝',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'diary',
                'requirement_value' => 1,
            ],
            [
                'code' => 'week_streak',
                'name' => 'Semana de Reflexión',
                'description' => 'Escribe en el diario 7 días seguidos',
                'icon' => '🔥',
                'color' => '#F59E0B',
                'points' => 50,
                'type' => 'diary',
                'requirement_value' => 7,
            ],
            [
                'code' => 'month_streak',
                'name' => 'Mes de Escritura',
                'description' => 'Escribe en el diario 30 días seguidos',
                'icon' => '⭐',
                'color' => '#8B5CF6',
                'points' => 200,
                'type' => 'diary',
                'requirement_value' => 30,
            ],
            [
                'code' => 'happy_writer',
                'name' => 'Escritora Feliz',
                'description' => 'Escribe 10 entradas con estados de ánimo felices',
                'icon' => '😊',
                'color' => '#10B981',
                'points' => 75,
                'type' => 'diary',
                'requirement_value' => 10,
            ],

            // Logros de Tareas
            [
                'code' => 'first_todo',
                'name' => 'Organizada',
                'description' => 'Crea tu primera tarea',
                'icon' => '✅',
                'color' => '#EC4899',
                'points' => 5,
                'type' => 'todo',
                'requirement_value' => 1,
            ],
            [
                'code' => 'todo_master',
                'name' => 'Maestra de Tareas',
                'description' => 'Completa 50 tareas',
                'icon' => '🏆',
                'color' => '#F59E0B',
                'points' => 100,
                'type' => 'todo',
                'requirement_value' => 50,
            ],

            // Logros de Hábitos
            [
                'code' => 'habit_streak_7',
                'name' => 'Rutina Establecida',
                'description' => 'Mantén un hábito por 7 días seguidos',
                'icon' => '💪',
                'color' => '#3B82F6',
                'points' => 50,
                'type' => 'habit',
                'requirement_value' => 7,
            ],
            [
                'code' => 'habit_streak_30',
                'name' => 'Maestra de Hábitos',
                'description' => 'Mantén un hábito por 30 días seguidos',
                'icon' => '👑',
                'color' => '#8B5CF6',
                'points' => 200,
                'type' => 'habit',
                'requirement_value' => 30,
            ],

            // Logros de Snoopy
            [
                'code' => 'snoopy_level_5',
                'name' => 'Cuidado de Snoopy',
                'description' => 'Lleva a Snoopy al nivel 5',
                'icon' => '🐶',
                'color' => '#EC4899',
                'points' => 50,
                'type' => 'pet',
                'requirement_value' => 5,
            ],
            [
                'code' => 'snoopy_level_10',
                'name' => 'Mejor Amiga de Snoopy',
                'description' => 'Lleva a Snoopy al nivel 10',
                'icon' => '💖',
                'color' => '#F59E0B',
                'points' => 150,
                'type' => 'pet',
                'requirement_value' => 10,
            ],
            [
                'code' => 'coins_collector',
                'name' => 'Coleccionista',
                'description' => 'Acumula 1000 fichitas',
                'icon' => '💰',
                'color' => '#10B981',
                'points' => 100,
                'type' => 'pet',
                'requirement_value' => 1000,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['code' => $achievement['code']],
                $achievement
            );
        }
    }
}
