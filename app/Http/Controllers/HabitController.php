<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAchievements;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Support\UserCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class HabitController extends Controller
{
    use HandlesAchievements;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $habits = Habit::where('user_id', Auth::id())
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

        $unlocked = $this->syncAchievements(['habit']);
        UserCache::forgetDashboard(Auth::id());

        return redirect()->route('habits.show', $habit->id)
            ->with('success', 'Hábito creado exitosamente.' . $this->achievementMessage($unlocked));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $habit = Habit::where('user_id', Auth::id())->findOrFail($id);

        $recentLogs = HabitLog::where('habit_id', $habit->id)
            ->where('user_id', Auth::id())
            ->orderByDesc('completed_at')
            ->limit(120)
            ->get();

        $habit->setRelation('habitLogs', $recentLogs->take(90)->values());

        // Usar las mismas fechas recientes para rachas (suficiente para streaks realistas)
        $logDates = $recentLogs
            ->pluck('completed_at')
            ->map(fn ($date) => Carbon::parse($date)->startOfDay()->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        $totalCompletions = HabitLog::where('habit_id', $habit->id)
            ->where('user_id', Auth::id())
            ->count();

        $thisMonthCompletions = HabitLog::where('habit_id', $habit->id)
            ->where('user_id', Auth::id())
            ->whereBetween('completed_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();

        $currentStreak = $this->calculateCurrentHabitStreak($logDates);
        $bestStreak = $this->calculateBestHabitStreak($logDates);

        return Inertia::render('Habits/Show', [
            'habit' => $habit,
            'currentStreak' => $currentStreak,
            'totalCompletions' => $totalCompletions,
            'thisMonthCompletions' => $thisMonthCompletions,
            'bestStreak' => $bestStreak,
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>  $sortedDates
     */
    private function calculateCurrentHabitStreak($sortedDates): int
    {
        if ($sortedDates->isEmpty()) {
            return 0;
        }

        $dateSet = array_flip($sortedDates->all());
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

            while (isset($dateSet[$cursor->format('Y-m-d')])) {
                $streak++;
                $cursor->subDay();
            }

            $best = max($best, $streak);
        }

        return $best;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>  $sortedDates
     */
    private function calculateBestHabitStreak($sortedDates): int
    {
        if ($sortedDates->isEmpty()) {
            return 0;
        }

        $best = 1;
        $current = 1;
        $dates = $sortedDates->values();

        for ($i = 1; $i < $dates->count(); $i++) {
            $prev = Carbon::parse($dates[$i - 1]);
            $curr = Carbon::parse($dates[$i]);

            if ($prev->copy()->addDay()->format('Y-m-d') === $curr->format('Y-m-d')) {
                $current++;
                $best = max($best, $current);
            } else {
                $current = 1;
            }
        }

        return $best;
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

        UserCache::forgetDashboard(Auth::id());

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

        UserCache::forgetDashboard(Auth::id());

        return redirect()->route('habits.index')
            ->with('success', 'Hábito eliminado exitosamente.');
    }
}
