<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Auth;

trait HandlesAchievements
{
    /**
     * @param  string[]  $types
     * @return Achievement[]
     */
    protected function syncAchievements(array $types = []): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $service = app(AchievementService::class);

        return $types === []
            ? $service->syncAll($user)
            : $service->syncTypes($user, $types);
    }

    /**
     * @param  Achievement[]  $unlocked
     */
    protected function achievementMessage(array $unlocked): string
    {
        if (empty($unlocked)) {
            return '';
        }

        $names = collect($unlocked)->pluck('name')->join(', ');

        return " ¡Desbloqueaste logros: {$names}! 🏆";
    }
}
