<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class UserCache
{
    /**
     * Invalida cachés de dashboard y estadísticas (usa el driver actual: database/file).
     */
    public static function forgetDashboard(?int $userId): void
    {
        if (! $userId) {
            return;
        }

        Cache::forget("dashboard.stats.{$userId}");
        Cache::forget("dashboard.week.{$userId}");
        Cache::forget("statistics_user_{$userId}");
        Cache::forget("user.{$userId}.daily_quote." . now()->toDateString());
    }
}
