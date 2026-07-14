<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function index(AchievementService $achievementService)
    {
        $user = Auth::user();

        $newlyUnlocked = $achievementService->syncAll($user);

        $allAchievements = Cache::remember(
            'achievements:all_list',
            now()->addHour(),
            fn () => Achievement::orderBy('type')->orderBy('points')->get()
        );

        $userAchievements = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->unique()
            ->values()
            ->toArray();

        $progress = $achievementService->getProgress($user);

        $responseData = [
            'achievements' => $allAchievements,
            'unlockedAchievements' => $userAchievements,
            'progress' => $progress,
        ];

        if (! empty($newlyUnlocked)) {
            $names = collect($newlyUnlocked)->pluck('name')->join(', ');
            session()->flash('success', "¡Se desbloquearon logros pendientes: {$names}! 🏆");
        }

        return Inertia::render('Achievements/Index', $responseData);
    }
}
