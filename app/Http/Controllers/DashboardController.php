<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\Note;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\Gratitude;
use App\Models\Pet;
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
            ],
        ]);
    }
}
