<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener todos los logros (sin filtrar por código único, mostrar todos)
        $allAchievements = Achievement::orderBy('type')->orderBy('points')->get();
        
        // Obtener logros desbloqueados del usuario
        $userAchievements = UserAchievement::where('user_id', $user->id)
            ->with('achievement')
            ->get()
            ->pluck('achievement_id')
            ->unique()
            ->values()
            ->toArray();

        return Inertia::render('Achievements/Index', [
            'achievements' => $allAchievements,
            'unlockedAchievements' => $userAchievements,
        ]);
    }
}
