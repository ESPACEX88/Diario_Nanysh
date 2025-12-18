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
            'upcomingEvents' => $upcomingEvents,
            'dailyQuote' => $dailyQuote,
        ]);
    }

    /**
     * Calculate writing streak for user.
     */
    private function calculateStreak($userId): int
    {
        $streak = 0;
        $checkDate = Carbon::today();
        
        // Si hoy no hay entrada, empezar desde ayer
        $todayEntry = DiaryEntry::where('user_id', $userId)
            ->whereDate('date', $checkDate)
            ->exists();
        
        if (!$todayEntry) {
            $checkDate->subDay();
        }
        
        // Contar días consecutivos hacia atrás (máximo 100 para evitar loops infinitos)
        $maxIterations = 100;
        while ($maxIterations-- > 0) {
            $hasEntry = DiaryEntry::where('user_id', $userId)
                ->whereDate('date', $checkDate)
                ->exists();
            
            if ($hasEntry) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }
}
