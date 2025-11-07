<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\DiaryEntry;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Verificar y otorgar logros relacionados con el diario
     */
    public function checkDiaryAchievements(User $user): array
    {
        $unlocked = [];
        
        // Primer entrada
        $firstEntry = Achievement::where('code', 'first_entry')->first();
        if ($firstEntry && !$this->hasAchievement($user, $firstEntry->id)) {
            $entryCount = DiaryEntry::where('user_id', $user->id)->count();
            if ($entryCount >= 1) {
                $this->unlockAchievement($user, $firstEntry);
                $unlocked[] = $firstEntry;
            }
        }
        
        // Racha de 7 días
        $weekStreak = Achievement::where('code', 'week_streak')->first();
        if ($weekStreak && !$this->hasAchievement($user, $weekStreak->id)) {
            if ($this->checkStreak($user, 7)) {
                $this->unlockAchievement($user, $weekStreak);
                $unlocked[] = $weekStreak;
            }
        }
        
        // Racha de 30 días
        $monthStreak = Achievement::where('code', 'month_streak')->first();
        if ($monthStreak && !$this->hasAchievement($user, $monthStreak->id)) {
            if ($this->checkStreak($user, 30)) {
                $this->unlockAchievement($user, $monthStreak);
                $unlocked[] = $monthStreak;
            }
        }
        
        // Escritora feliz (10 entradas con estados felices)
        $happyWriter = Achievement::where('code', 'happy_writer')->first();
        if ($happyWriter && !$this->hasAchievement($user, $happyWriter->id)) {
            $happyMoods = ['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'];
            $happyEntries = DiaryEntry::where('user_id', $user->id)
                ->whereIn('mood', $happyMoods)
                ->count();
            if ($happyEntries >= 10) {
                $this->unlockAchievement($user, $happyWriter);
                $unlocked[] = $happyWriter;
            }
        }
        
        return $unlocked;
    }
    
    /**
     * Verificar y otorgar logros relacionados con tareas
     */
    public function checkTodoAchievements(User $user): array
    {
        $unlocked = [];
        
        // Primera tarea completada
        $firstTodo = Achievement::where('code', 'first_todo')->first();
        if ($firstTodo && !$this->hasAchievement($user, $firstTodo->id)) {
            $completedCount = Todo::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();
            if ($completedCount >= 1) {
                $this->unlockAchievement($user, $firstTodo);
                $unlocked[] = $firstTodo;
            }
        }
        
        // 10 tareas completadas
        $todoMaster = Achievement::where('code', 'todo_master')->first();
        if ($todoMaster && !$this->hasAchievement($user, $todoMaster->id)) {
            $completedCount = Todo::where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();
            if ($completedCount >= 10) {
                $this->unlockAchievement($user, $todoMaster);
                $unlocked[] = $todoMaster;
            }
        }
        
        return $unlocked;
    }
    
    /**
     * Verificar si el usuario tiene un logro
     */
    private function hasAchievement(User $user, int $achievementId): bool
    {
        return UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievementId)
            ->exists();
    }
    
    /**
     * Otorgar un logro al usuario
     */
    private function unlockAchievement(User $user, Achievement $achievement): void
    {
        UserAchievement::firstOrCreate(
            [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ],
            [
                'unlocked_at' => now(),
            ]
        );
        
        // Agregar puntos a la mascota si existe
        $pet = $user->pet;
        if ($pet) {
            $pet->coins += $achievement->points;
            $pet->save();
        }
    }
    
    /**
     * Verificar racha de días consecutivos
     */
    private function checkStreak(User $user, int $days): bool
    {
        $entries = DiaryEntry::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit($days)
            ->get()
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
        
        if (count($entries) < $days) {
            return false;
        }
        
        // Verificar que sean días consecutivos
        $today = now()->format('Y-m-d');
        for ($i = 0; $i < $days; $i++) {
            $expectedDate = date('Y-m-d', strtotime("-$i days", strtotime($today)));
            if (!in_array($expectedDate, $entries)) {
                return false;
            }
        }
        
        return true;
    }
}

