<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HabitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $habits = Habit::where('user_id', Auth::id())
            ->with(['habitLogs'])
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Habits/Index', [
            'habits' => $habits,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Habits/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'reminder_time' => 'nullable|date',
        ]);

        $habit = Habit::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'] ?? 'daily',
            'color' => $validated['color'] ?? '#3b82f6',
            'icon' => $validated['icon'] ?? null,
            'reminder_time' => $validated['reminder_time'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('habits.show', $habit->id)
            ->with('success', 'Hábito creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $habit = Habit::where('user_id', Auth::id())
            ->with(['habitLogs' => function($query) {
                $query->orderBy('completed_at', 'desc')->limit(90);
            }])
            ->findOrFail($id);

        // Calculate current streak
        $currentStreak = 0;
        $checkDate = \Carbon\Carbon::today();
        $logs = $habit->habitLogs->sortByDesc('completed_at');
        
        foreach ($logs as $log) {
            $logDate = \Carbon\Carbon::parse($log->completed_at)->startOfDay();
            if ($logDate->isSameDay($checkDate) || $logDate->isSameDay($checkDate->copy()->subDay())) {
                if ($logDate->isSameDay($checkDate)) {
                    $currentStreak++;
                } else {
                    $currentStreak++;
                    $checkDate = $logDate;
                }
            } else {
                break;
            }
        }

        // Calculate total completions
        $totalCompletions = $habit->habitLogs->count();
        
        // Calculate this month completions
        $thisMonthCompletions = $habit->habitLogs
            ->filter(function($log) {
                return \Carbon\Carbon::parse($log->completed_at)->isCurrentMonth();
            })
            ->count();

        // Calculate best streak
        $bestStreak = 0;
        $tempStreak = 0;
        $lastDate = null;
        
        foreach ($logs->sortBy('completed_at') as $log) {
            $logDate = \Carbon\Carbon::parse($log->completed_at)->startOfDay();
            
            if ($lastDate === null) {
                $tempStreak = 1;
            } elseif ($logDate->diffInDays($lastDate) === 1) {
                $tempStreak++;
            } else {
                $bestStreak = max($bestStreak, $tempStreak);
                $tempStreak = 1;
            }
            
            $lastDate = $logDate;
        }
        $bestStreak = max($bestStreak, $tempStreak);

        return Inertia::render('Habits/Show', [
            'habit' => $habit,
            'currentStreak' => $currentStreak,
            'totalCompletions' => $totalCompletions,
            'thisMonthCompletions' => $thisMonthCompletions,
            'bestStreak' => $bestStreak,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $habit = Habit::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Habits/Edit', [
            'habit' => $habit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $habit = Habit::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'reminder_time' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $habit->update($validated);

        return redirect()->route('habits.show', $habit->id)
            ->with('success', 'Hábito actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $habit = Habit::where('user_id', Auth::id())
            ->findOrFail($id);

        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Hábito eliminado exitosamente.');
    }
}
