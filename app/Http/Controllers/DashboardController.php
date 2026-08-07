<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\Note;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\Gratitude;
use App\Models\Pet;
use App\Models\Todo;
use App\Models\Event;
use App\Models\MotivationalQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Recent diary entries (con eager loading para optimizar)
        $recentEntries = DiaryEntry::where('user_id', $user->id)
            ->with(['tags'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Today's entry
        $todayEntry = DiaryEntry::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        // Statistics con caché de 5 minutos
        $stats = Cache::remember("dashboard.stats.{$user->id}", 300, function () use ($user) {
            return [
                'totalEntries' => DiaryEntry::where('user_id', $user->id)->count(),
                'favoriteEntries' => DiaryEntry::where('user_id', $user->id)
                    ->where('is_favorite', true)
                    ->count(),
                'totalNotes' => Note::where('user_id', $user->id)->count(),
                'pinnedNotes' => Note::where('user_id', $user->id)
                    ->where('is_pinned', true)
                    ->count(),
                'activeGoals' => Goal::where('user_id', $user->id)
                    ->where('is_completed', false)
                    ->count(),
                'activeHabits' => Habit::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->count(),
            ];
        });

        // This week's statistics (caché de 10 minutos)
        $weekStats = Cache::remember("dashboard.week.{$user->id}", 600, function () use ($user) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            return [
                'thisWeekGratitudes' => Gratitude::where('user_id', $user->id)
                    ->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->count(),
                'thisWeekEntries' => DiaryEntry::where('user_id', $user->id)
                    ->whereBetween('date', [$startOfWeek, $endOfWeek])
                    ->count(),
                'completedTodosThisWeek' => Todo::where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                    ->count(),
            ];
        });

        // Calculate writing streak - sin caché ya que cambia diariamente
        $streak = $this->calculateStreak($user->id);

        // Pending todos
        $pendingTodos = Todo::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderBy('due_date')
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get();

        // Quick habit shortcuts for dashboard
        $activeHabitsQuick = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get(['id', 'name', 'icon', 'is_active']);

        // Upcoming events (next 7 days)
        $upcomingEvents = Event::where('user_id', $user->id)
            ->where('start_date', '>=', Carbon::now())
            ->where('start_date', '<=', Carbon::now()->addDays(7))
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Daily motivational quote
        $dailyQuote = MotivationalQuote::getDailyQuote();

        // Get or create pet
        $pet = Pet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Snoopy',
                'level' => 1,
                'experience' => 0,
                'happiness' => 100,
                'hunger' => 100,
                'energy' => 100,
                'health' => 100,
                'coins' => 50, // Starting coins
            ]
        );
        $pet->decreaseStats();
        $pet->save();

        return Inertia::render('Dashboard', [
            'recentEntries' => $recentEntries,
            'todayEntry' => $todayEntry,
            'pet' => [
                'name' => $pet->name,
                'level' => (int) $pet->level,
                'happiness' => (int) $pet->happiness,
                'hunger' => (int) $pet->hunger,
                'energy' => (int) $pet->energy,
                'health' => (int) $pet->health,
                'coins' => (int) $pet->coins,
                'mood' => $pet->mood,
            ],
            'stats' => array_merge($stats, $weekStats, ['streak' => $streak]),
            'pendingTodos' => $pendingTodos,
            'activeHabitsQuick' => $activeHabitsQuick,
            'upcomingEvents' => $upcomingEvents,
            'dailyQuote' => $dailyQuote,
        ]);
    }

    /**
     * Calculate writing streak for user with a single query.
     */
    private function calculateStreak($userId): int
    {
        $dates = DiaryEntry::where('user_id', $userId)
            ->select('date')
            ->distinct()
            ->orderByDesc('date')
            ->limit(120)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->all();

        if ($dates === []) {
            return 0;
        }

        $dateSet = array_flip($dates);
        $anchors = [
            Carbon::today()->format('Y-m-d'),
            Carbon::yesterday()->format('Y-m-d'),
        ];

        $best = 0;

        foreach ($anchors as $anchor) {
            if (! isset($dateSet[$anchor])) {
                continue;
            }

            $streak = 0;
            $cursor = Carbon::parse($anchor);

            while (isset($dateSet[$cursor->format('Y-m-d')]) && $streak < 100) {
                $streak++;
                $cursor->subDay();
            }

            $best = max($best, $streak);
        }

        return $best;
    }
}
