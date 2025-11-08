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

        // Recent diary entries
        $recentEntries = DiaryEntry::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Today's entry
        $todayEntry = DiaryEntry::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        // Statistics
        $totalEntries = DiaryEntry::where('user_id', $user->id)->count();
        $favoriteEntries = DiaryEntry::where('user_id', $user->id)
            ->where('is_favorite', true)
            ->count();
        
        $totalNotes = Note::where('user_id', $user->id)->count();
        $pinnedNotes = Note::where('user_id', $user->id)
            ->where('is_pinned', true)
            ->count();

        $activeGoals = Goal::where('user_id', $user->id)
            ->where('is_completed', false)
            ->count();

        $activeHabits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();

        // This week's gratitudes
        $thisWeekGratitudes = Gratitude::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Calculate writing streak
        $streak = 0;
        $currentDate = Carbon::today();
        $checkDate = $currentDate->copy();
        
        while (true) {
            $entry = DiaryEntry::where('user_id', $user->id)
                ->whereDate('date', $checkDate)
                ->first();
            
            if ($entry) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        // This week's entries
        $thisWeekEntries = DiaryEntry::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Completed todos this week
        $completedTodosThisWeek = Todo::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

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
            'stats' => [
                'totalEntries' => $totalEntries,
                'favoriteEntries' => $favoriteEntries,
                'totalNotes' => $totalNotes,
                'pinnedNotes' => $pinnedNotes,
                'activeGoals' => $activeGoals,
                'activeHabits' => $activeHabits,
                'thisWeekGratitudes' => $thisWeekGratitudes,
                'streak' => $streak,
                'thisWeekEntries' => $thisWeekEntries,
                'completedTodosThisWeek' => $completedTodosThisWeek,
            ],
            'pendingTodos' => $pendingTodos,
            'upcomingEvents' => $upcomingEvents,
            'dailyQuote' => $dailyQuote,
        ]);
    }
}
