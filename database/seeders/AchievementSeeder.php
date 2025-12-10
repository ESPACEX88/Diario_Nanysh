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

            // Logros adicionales de Diario
            [
                'code' => 'diary_entries_10',
                'name' => 'Escritora Novata',
                'description' => 'Escribe 10 entradas en el diario',
                'icon' => '📖',
                'color' => '#EC4899',
                'points' => 25,
                'type' => 'diary',
                'requirement_value' => 10,
            ],
            [
                'code' => 'diary_entries_50',
                'name' => 'Escritora Experta',
                'description' => 'Escribe 50 entradas en el diario',
                'icon' => '📚',
                'color' => '#F59E0B',
                'points' => 150,
                'type' => 'diary',
                'requirement_value' => 50,
            ],
            [
                'code' => 'diary_entries_100',
                'name' => 'Escritora Maestra',
                'description' => 'Escribe 100 entradas en el diario',
                'icon' => '📜',
                'color' => '#8B5CF6',
                'points' => 500,
                'type' => 'diary',
                'requirement_value' => 100,
            ],
            [
                'code' => 'favorite_entries_5',
                'name' => 'Memorias Especiales',
                'description' => 'Marca 5 entradas como favoritas',
                'icon' => '⭐',
                'color' => '#F59E0B',
                'points' => 30,
                'type' => 'diary',
                'requirement_value' => 5,
            ],

            // Logros adicionales de Tareas
            [
                'code' => 'todo_completed_10',
                'name' => 'Productiva',
                'description' => 'Completa 10 tareas',
                'icon' => '✅',
                'color' => '#10B981',
                'points' => 30,
                'type' => 'todo',
                'requirement_value' => 10,
            ],
            [
                'code' => 'todo_completed_100',
                'name' => 'Super Productiva',
                'description' => 'Completa 100 tareas',
                'icon' => '🎯',
                'color' => '#8B5CF6',
                'points' => 300,
                'type' => 'todo',
                'requirement_value' => 100,
            ],

            // Logros adicionales de Hábitos
            [
                'code' => 'habit_streak_100',
                'name' => 'Leyenda de Hábitos',
                'description' => 'Mantén un hábito por 100 días seguidos',
                'icon' => '🏅',
                'color' => '#EC4899',
                'points' => 1000,
                'type' => 'habit',
                'requirement_value' => 100,
            ],
            [
                'code' => 'habits_created_5',
                'name' => 'Creadora de Rutinas',
                'description' => 'Crea 5 hábitos diferentes',
                'icon' => '🔄',
                'color' => '#3B82F6',
                'points' => 75,
                'type' => 'habit',
                'requirement_value' => 5,
            ],

            // Logros de Eventos
            [
                'code' => 'first_event',
                'name' => 'Organizadora',
                'description' => 'Crea tu primer evento',
                'icon' => '📅',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'event',
                'requirement_value' => 1,
            ],
            [
                'code' => 'events_created_10',
                'name' => 'Planificadora',
                'description' => 'Crea 10 eventos',
                'icon' => '🗓️',
                'color' => '#F59E0B',
                'points' => 50,
                'type' => 'event',
                'requirement_value' => 10,
            ],

            // Logros de Sueños
            [
                'code' => 'first_dream',
                'name' => 'Soñadora',
                'description' => 'Registra tu primer sueño',
                'icon' => '💭',
                'color' => '#8B5CF6',
                'points' => 15,
                'type' => 'dream',
                'requirement_value' => 1,
            ],
            [
                'code' => 'dreams_recorded_20',
                'name' => 'Intérprete de Sueños',
                'description' => 'Registra 20 sueños',
                'icon' => '🌙',
                'color' => '#3B82F6',
                'points' => 100,
                'type' => 'dream',
                'requirement_value' => 20,
            ],

            // Logros de Media
            [
                'code' => 'first_media',
                'name' => 'Crítica',
                'description' => 'Agrega tu primer libro o película',
                'icon' => '📚',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'media',
                'requirement_value' => 1,
            ],
            [
                'code' => 'media_reviewed_20',
                'name' => 'Crítica Experta',
                'description' => 'Revisa 20 libros o películas',
                'icon' => '⭐',
                'color' => '#F59E0B',
                'points' => 150,
                'type' => 'media',
                'requirement_value' => 20,
            ],

            // Logros de Fotos
            [
                'code' => 'first_photo',
                'name' => 'Fotógrafa',
                'description' => 'Sube tu primera foto',
                'icon' => '📸',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'photo',
                'requirement_value' => 1,
            ],
            [
                'code' => 'photos_uploaded_50',
                'name' => 'Colección de Recuerdos',
                'description' => 'Sube 50 fotos',
                'icon' => '🖼️',
                'color' => '#8B5CF6',
                'points' => 200,
                'type' => 'photo',
                'requirement_value' => 50,
            ],

            // Logros de Ciclo
            [
                'code' => 'first_cycle_tracking',
                'name' => 'Autocuidado',
                'description' => 'Registra tu primer seguimiento de ciclo',
                'icon' => '🌸',
                'color' => '#EC4899',
                'points' => 15,
                'type' => 'cycle',
                'requirement_value' => 1,
            ],
            [
                'code' => 'cycle_tracked_30',
                'name' => 'Conocimiento Personal',
                'description' => 'Registra 30 seguimientos de ciclo',
                'icon' => '🌺',
                'color' => '#8B5CF6',
                'points' => 150,
                'type' => 'cycle',
                'requirement_value' => 30,
            ],

            // Logros de Comidas
            [
                'code' => 'first_meal',
                'name' => 'Chef',
                'description' => 'Agrega tu primera comida favorita',
                'icon' => '🍽️',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'meal',
                'requirement_value' => 1,
            ],
            [
                'code' => 'meals_added_20',
                'name' => 'Gourmet',
                'description' => 'Agrega 20 comidas favoritas',
                'icon' => '👨‍🍳',
                'color' => '#F59E0B',
                'points' => 100,
                'type' => 'meal',
                'requirement_value' => 20,
            ],

            // Logros de Lista de Deseos
            [
                'code' => 'first_wishlist',
                'name' => 'Soñadora',
                'description' => 'Agrega tu primer deseo',
                'icon' => '💫',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'wishlist',
                'requirement_value' => 1,
            ],
            [
                'code' => 'wishlist_obtained_10',
                'name' => 'Realizadora de Sueños',
                'description' => 'Obtén 10 deseos de tu lista',
                'icon' => '✨',
                'color' => '#10B981',
                'points' => 200,
                'type' => 'wishlist',
                'requirement_value' => 10,
            ],

            // Logros de Contadores
            [
                'code' => 'first_counter',
                'name' => 'Contadora',
                'description' => 'Crea tu primer contador de días',
                'icon' => '📅',
                'color' => '#EC4899',
                'points' => 10,
                'type' => 'counter',
                'requirement_value' => 1,
            ],
            [
                'code' => 'counter_100_days',
                'name' => 'Celebración',
                'description' => 'Llega a 100 días en un contador',
                'icon' => '🎉',
                'color' => '#F59E0B',
                'points' => 150,
                'type' => 'counter',
                'requirement_value' => 100,
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
