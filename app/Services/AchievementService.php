<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\CycleTracking;
use App\Models\DayCounter;
use App\Models\DiaryEntry;
use App\Models\Dream;
use App\Models\Event;
use App\Models\FavoriteMeal;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\MediaItem;
use App\Models\Pet;
use App\Models\Photo;
use App\Models\Todo;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\WishlistItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AchievementService
{
    private const HAPPY_MOODS = [
        '😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋',
        '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗',
        '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤',
        '💯', '🔥', '⭐', '💫', '☀️', '🌙',
    ];

    /** @var Collection<string, Achievement>|null */
    private ?Collection $achievementsByCode = null;

    /** @var array<int, true>|null */
    private ?array $unlockedIds = null;

    private ?User $currentUser = null;

    /**
     * Sincroniza todos los logros del usuario (incluye verificación retroactiva).
     *
     * @return Achievement[]
     */
    public function syncAll(User $user): array
    {
        $this->resetContext();
        $this->loadContext($user);

        $unlocked = [];

        foreach ($this->getChecks() as $check) {
            $unlocked = array_merge($unlocked, $check($user));
        }

        return $unlocked;
    }

    /**
     * Sincroniza logros de tipos específicos.
     *
     * @param  string[]  $types
     * @return Achievement[]
     */
    public function syncTypes(User $user, array $types): array
    {
        $this->resetContext();
        $this->loadContext($user);

        $unlocked = [];

        foreach ($this->getChecks() as $type => $check) {
            if (in_array($type, $types, true)) {
                $unlocked = array_merge($unlocked, $check($user));
            }
        }

        return $unlocked;
    }

    /** @deprecated Usar syncTypes($user, ['diary']) */
    public function checkDiaryAchievements(User $user): array
    {
        return $this->syncTypes($user, ['diary']);
    }

    /** @deprecated Usar syncTypes($user, ['todo']) */
    public function checkTodoAchievements(User $user): array
    {
        return $this->syncTypes($user, ['todo']);
    }

    /**
     * Progreso actual de cada logro para mostrar en la UI.
     *
     * @return array<string, array{current: int, target: int, percent: float}>
     */
    public function getProgress(User $user): array
    {
        $this->resetContext();
        $this->loadContext($user);

        $metrics = $this->collectProgressMetrics($user);
        $progress = [];

        foreach ($this->achievementsByCode ?? [] as $code => $achievement) {
            $target = max(1, (int) ($achievement->requirement_value ?? 1));
            $current = min($target, (int) ($metrics[$code] ?? 0));

            $progress[$code] = [
                'current' => $current,
                'target' => $target,
                'percent' => round(($current / $target) * 100, 1),
            ];
        }

        return $progress;
    }

    /**
     * @return array<string, int>
     */
    private function collectProgressMetrics(User $user): array
    {
        $entryCount = DiaryEntry::where('user_id', $user->id)->count();
        $favoriteCount = DiaryEntry::where('user_id', $user->id)->where('is_favorite', true)->count();
        $happyCount = DiaryEntry::where('user_id', $user->id)->whereIn('mood', self::HAPPY_MOODS)->count();
        $diaryStreak = $this->getCurrentDiaryStreak($user);

        $totalTodos = Todo::where('user_id', $user->id)->count();
        $completedTodos = Todo::where('user_id', $user->id)->where('is_completed', true)->count();

        $habitsCount = Habit::where('user_id', $user->id)->count();
        $maxHabitStreak = $this->getMaxHabitStreak($user);

        $pet = Pet::where('user_id', $user->id)->first();
        $petLevel = (int) ($pet?->level ?? 0);
        $petCoins = (int) ($pet?->coins ?? 0);

        $eventsCount = Event::where('user_id', $user->id)->count();
        $dreamsCount = Dream::where('user_id', $user->id)->count();
        $mediaTotal = MediaItem::where('user_id', $user->id)->count();
        $mediaReviewed = MediaItem::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhereNotNull('rating')
                    ->orWhereNotNull('review');
            })
            ->count();
        $photosCount = Photo::where('user_id', $user->id)->count();
        $cycleCount = CycleTracking::where('user_id', $user->id)->count();
        $mealsCount = FavoriteMeal::where('user_id', $user->id)->count();
        $wishlistTotal = WishlistItem::where('user_id', $user->id)->count();
        $wishlistObtained = WishlistItem::where('user_id', $user->id)->where('is_obtained', true)->count();
        $counters = DayCounter::where('user_id', $user->id)->get();
        $maxCounterDays = $counters->max(fn (DayCounter $counter) => max(0, $counter->days_count)) ?? 0;

        return [
            'first_entry' => $entryCount,
            'diary_entries_10' => $entryCount,
            'diary_entries_50' => $entryCount,
            'diary_entries_100' => $entryCount,
            'favorite_entries_5' => $favoriteCount,
            'happy_writer' => $happyCount,
            'week_streak' => $diaryStreak,
            'month_streak' => $diaryStreak,
            'first_todo' => $totalTodos,
            'todo_completed_10' => $completedTodos,
            'todo_master' => $completedTodos,
            'todo_completed_100' => $completedTodos,
            'habits_created_5' => $habitsCount,
            'habit_streak_7' => $maxHabitStreak,
            'habit_streak_30' => $maxHabitStreak,
            'habit_streak_100' => $maxHabitStreak,
            'snoopy_level_5' => $petLevel,
            'snoopy_level_10' => $petLevel,
            'coins_collector' => $petCoins,
            'first_event' => $eventsCount,
            'events_created_10' => $eventsCount,
            'first_dream' => $dreamsCount,
            'dreams_recorded_20' => $dreamsCount,
            'first_media' => $mediaTotal,
            'media_reviewed_20' => $mediaReviewed,
            'first_photo' => $photosCount,
            'photos_uploaded_50' => $photosCount,
            'first_cycle_tracking' => $cycleCount,
            'cycle_tracked_30' => $cycleCount,
            'first_meal' => $mealsCount,
            'meals_added_20' => $mealsCount,
            'first_wishlist' => $wishlistTotal,
            'wishlist_obtained_10' => $wishlistObtained,
            'first_counter' => $counters->count(),
            'counter_100_days' => (int) $maxCounterDays,
        ];
    }

    private function getCurrentDiaryStreak(User $user): int
    {
        $dates = DiaryEntry::where('user_id', $user->id)
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();

        if ($dates === []) {
            return 0;
        }

        $anchors = [
            now()->format('Y-m-d'),
            now()->subDay()->format('Y-m-d'),
        ];

        $best = 0;

        foreach ($anchors as $anchor) {
            if (! in_array($anchor, $dates, true)) {
                continue;
            }

            $streak = 0;
            while (in_array(Carbon::parse($anchor)->subDays($streak)->format('Y-m-d'), $dates, true)) {
                $streak++;
            }

            $best = max($best, $streak);
        }

        return $best;
    }

    /**
     * @return array<string, callable(User): array>
     */
    private function getChecks(): array
    {
        return [
            'diary' => fn (User $user) => $this->checkDiary($user),
            'todo' => fn (User $user) => $this->checkTodo($user),
            'habit' => fn (User $user) => $this->checkHabit($user),
            'pet' => fn (User $user) => $this->checkPet($user),
            'event' => fn (User $user) => $this->checkEvent($user),
            'dream' => fn (User $user) => $this->checkDream($user),
            'media' => fn (User $user) => $this->checkMedia($user),
            'photo' => fn (User $user) => $this->checkPhoto($user),
            'cycle' => fn (User $user) => $this->checkCycle($user),
            'meal' => fn (User $user) => $this->checkMeal($user),
            'wishlist' => fn (User $user) => $this->checkWishlist($user),
            'counter' => fn (User $user) => $this->checkCounter($user),
        ];
    }

    private function resetContext(): void
    {
        $this->achievementsByCode = null;
        $this->unlockedIds = null;
        $this->currentUser = null;
    }

    private function loadContext(User $user): void
    {
        $this->currentUser = $user;

        $this->achievementsByCode = Cache::remember(
            'achievements:all_by_code',
            now()->addHour(),
            fn () => Achievement::all()->keyBy('code')
        );

        $this->unlockedIds = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->flip()
            ->all();
    }

    /** @return Achievement[] */
    private function checkDiary(User $user): array
    {
        $unlocked = [];
        $entryCount = DiaryEntry::where('user_id', $user->id)->count();

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'first_entry',
            $entryCount >= $this->requirement('first_entry', 1)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'diary_entries_10',
            $entryCount >= $this->requirement('diary_entries_10', 10)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'diary_entries_50',
            $entryCount >= $this->requirement('diary_entries_50', 50)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'diary_entries_100',
            $entryCount >= $this->requirement('diary_entries_100', 100)
        ));

        $favoriteCount = DiaryEntry::where('user_id', $user->id)
            ->where('is_favorite', true)
            ->count();

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'favorite_entries_5',
            $favoriteCount >= $this->requirement('favorite_entries_5', 5)
        ));

        $happyCount = DiaryEntry::where('user_id', $user->id)
            ->whereIn('mood', self::HAPPY_MOODS)
            ->count();

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'happy_writer',
            $happyCount >= $this->requirement('happy_writer', 10)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'week_streak',
            $this->hasDiaryStreak($user, $this->requirement('week_streak', 7))
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'month_streak',
            $this->hasDiaryStreak($user, $this->requirement('month_streak', 30))
        ));

        return $unlocked;
    }

    /** @return Achievement[] */
    private function checkTodo(User $user): array
    {
        $unlocked = [];
        $totalTodos = Todo::where('user_id', $user->id)->count();
        $completedTodos = Todo::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'first_todo',
            $totalTodos >= $this->requirement('first_todo', 1)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'todo_completed_10',
            $completedTodos >= $this->requirement('todo_completed_10', 10)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'todo_master',
            $completedTodos >= $this->requirement('todo_master', 50)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'todo_completed_100',
            $completedTodos >= $this->requirement('todo_completed_100', 100)
        ));

        return $unlocked;
    }

    /** @return Achievement[] */
    private function checkHabit(User $user): array
    {
        $unlocked = [];
        $habitsCount = Habit::where('user_id', $user->id)->count();
        $maxStreak = $this->getMaxHabitStreak($user);

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'habits_created_5',
            $habitsCount >= $this->requirement('habits_created_5', 5)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'habit_streak_7',
            $maxStreak >= $this->requirement('habit_streak_7', 7)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'habit_streak_30',
            $maxStreak >= $this->requirement('habit_streak_30', 30)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'habit_streak_100',
            $maxStreak >= $this->requirement('habit_streak_100', 100)
        ));

        return $unlocked;
    }

    /** @return Achievement[] */
    private function checkPet(User $user): array
    {
        $unlocked = [];
        $pet = Pet::where('user_id', $user->id)->first();

        if (! $pet) {
            return $unlocked;
        }

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'snoopy_level_5',
            $pet->level >= $this->requirement('snoopy_level_5', 5)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'snoopy_level_10',
            $pet->level >= $this->requirement('snoopy_level_10', 10)
        ));

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'coins_collector',
            $pet->coins >= $this->requirement('coins_collector', 1000)
        ));

        return $unlocked;
    }

    /** @return Achievement[] */
    private function checkEvent(User $user): array
    {
        $count = Event::where('user_id', $user->id)->count();

        return array_merge(
            $this->unlockIf('first_event', $count >= $this->requirement('first_event', 1)),
            $this->unlockIf('events_created_10', $count >= $this->requirement('events_created_10', 10))
        );
    }

    /** @return Achievement[] */
    private function checkDream(User $user): array
    {
        $count = Dream::where('user_id', $user->id)->count();

        return array_merge(
            $this->unlockIf('first_dream', $count >= $this->requirement('first_dream', 1)),
            $this->unlockIf('dreams_recorded_20', $count >= $this->requirement('dreams_recorded_20', 20))
        );
    }

    /** @return Achievement[] */
    private function checkMedia(User $user): array
    {
        $total = MediaItem::where('user_id', $user->id)->count();
        $reviewed = MediaItem::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhereNotNull('rating')
                    ->orWhereNotNull('review');
            })
            ->count();

        return array_merge(
            $this->unlockIf('first_media', $total >= $this->requirement('first_media', 1)),
            $this->unlockIf('media_reviewed_20', $reviewed >= $this->requirement('media_reviewed_20', 20))
        );
    }

    /** @return Achievement[] */
    private function checkPhoto(User $user): array
    {
        $count = Photo::where('user_id', $user->id)->count();

        return array_merge(
            $this->unlockIf('first_photo', $count >= $this->requirement('first_photo', 1)),
            $this->unlockIf('photos_uploaded_50', $count >= $this->requirement('photos_uploaded_50', 50))
        );
    }

    /** @return Achievement[] */
    private function checkCycle(User $user): array
    {
        $count = CycleTracking::where('user_id', $user->id)->count();

        return array_merge(
            $this->unlockIf('first_cycle_tracking', $count >= $this->requirement('first_cycle_tracking', 1)),
            $this->unlockIf('cycle_tracked_30', $count >= $this->requirement('cycle_tracked_30', 30))
        );
    }

    /** @return Achievement[] */
    private function checkMeal(User $user): array
    {
        $count = FavoriteMeal::where('user_id', $user->id)->count();

        return array_merge(
            $this->unlockIf('first_meal', $count >= $this->requirement('first_meal', 1)),
            $this->unlockIf('meals_added_20', $count >= $this->requirement('meals_added_20', 20))
        );
    }

    /** @return Achievement[] */
    private function checkWishlist(User $user): array
    {
        $total = WishlistItem::where('user_id', $user->id)->count();
        $obtained = WishlistItem::where('user_id', $user->id)
            ->where('is_obtained', true)
            ->count();

        return array_merge(
            $this->unlockIf('first_wishlist', $total >= $this->requirement('first_wishlist', 1)),
            $this->unlockIf('wishlist_obtained_10', $obtained >= $this->requirement('wishlist_obtained_10', 10))
        );
    }

    /** @return Achievement[] */
    private function checkCounter(User $user): array
    {
        $unlocked = [];
        $counters = DayCounter::where('user_id', $user->id)->get();

        $unlocked = array_merge($unlocked, $this->unlockIf(
            'first_counter',
            $counters->count() >= $this->requirement('first_counter', 1)
        ));

        $hasHundredDays = $counters->contains(
            fn (DayCounter $counter) => $counter->days_count >= $this->requirement('counter_100_days', 100)
        );

        $unlocked = array_merge($unlocked, $this->unlockIf('counter_100_days', $hasHundredDays));

        return $unlocked;
    }

    private function hasDiaryStreak(User $user, int $requiredDays): bool
    {
        $dates = DiaryEntry::where('user_id', $user->id)
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();

        if (count($dates) < $requiredDays) {
            return false;
        }

        $anchors = [
            now()->format('Y-m-d'),
            now()->subDay()->format('Y-m-d'),
        ];

        foreach ($anchors as $anchor) {
            if ($this->hasConsecutiveStreakFromAnchor($dates, $anchor, $requiredDays)) {
                return true;
            }
        }

        return false;
    }

    private function hasConsecutiveStreakFromAnchor(array $dates, string $anchor, int $requiredDays): bool
    {
        if (! in_array($anchor, $dates, true)) {
            return false;
        }

        for ($i = 0; $i < $requiredDays; $i++) {
            $expected = Carbon::parse($anchor)->subDays($i)->format('Y-m-d');
            if (! in_array($expected, $dates, true)) {
                return false;
            }
        }

        return true;
    }

    private function getMaxHabitStreak(User $user): int
    {
        $logsByHabit = HabitLog::where('user_id', $user->id)
            ->select('habit_id', 'completed_at')
            ->get()
            ->groupBy('habit_id');

        $maxStreak = 0;

        foreach ($logsByHabit as $logs) {
            $dates = $logs
                ->pluck('completed_at')
                ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique()
                ->sort()
                ->values()
                ->all();

            $maxStreak = max($maxStreak, $this->longestConsecutiveStreak($dates));
        }

        return $maxStreak;
    }

    private function longestConsecutiveStreak(array $sortedDates): int
    {
        if (empty($sortedDates)) {
            return 0;
        }

        $max = 1;
        $current = 1;

        for ($i = 1, $count = count($sortedDates); $i < $count; $i++) {
            $prev = Carbon::parse($sortedDates[$i - 1]);
            $curr = Carbon::parse($sortedDates[$i]);

            if ($prev->copy()->addDay()->format('Y-m-d') === $curr->format('Y-m-d')) {
                $current++;
                $max = max($max, $current);
            } else {
                $current = 1;
            }
        }

        return $max;
    }

    private function requirement(string $code, int $fallback): int
    {
        $achievement = $this->achievementsByCode?->get($code);

        return $achievement?->requirement_value ?? $fallback;
    }

    /** @return Achievement[] */
    private function unlockIf(string $code, bool $condition): array
    {
        if (! $condition) {
            return [];
        }

        $achievement = $this->achievementsByCode?->get($code);

        if (! $achievement || $this->hasAchievement($achievement->id)) {
            return [];
        }

        $this->unlockAchievement($achievement);

        return [$achievement];
    }

    private function hasAchievement(int $achievementId): bool
    {
        return isset($this->unlockedIds[$achievementId]);
    }

    private function unlockAchievement(Achievement $achievement): void
    {
        if (! $this->currentUser) {
            return;
        }

        UserAchievement::firstOrCreate(
            [
                'user_id' => $this->currentUser->id,
                'achievement_id' => $achievement->id,
            ],
            ['unlocked_at' => now()]
        );

        $this->unlockedIds[$achievement->id] = true;

        $pet = Pet::where('user_id', $this->currentUser->id)->first();
        if ($pet) {
            $pet->increment('coins', $achievement->points);
        }
    }
}
