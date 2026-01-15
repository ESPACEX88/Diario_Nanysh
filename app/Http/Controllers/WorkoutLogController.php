<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class WorkoutLogController extends Controller
{
    /**
     * Display a listing of the resource with calendar view.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Obtener todos los entrenamientos del mes
        $workouts = WorkoutLog::where('user_id', Auth::id())
            ->whereYear('workout_date', $year)
            ->whereMonth('workout_date', $month)
            ->orderBy('workout_date', 'desc')
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'date' => $workout->workout_date->format('Y-m-d'),
                    'routine_name' => $workout->routine_name,
                    'intensity' => $workout->intensity,
                    'duration_minutes' => $workout->duration_minutes,
                    'exercises' => $workout->exercises,
                    'notes' => $workout->notes,
                ];
            });

        // Obtener estadísticas del mes
        $stats = [
            'total_workouts' => $workouts->count(),
            'total_minutes' => $workouts->sum('duration_minutes'),
            'streak' => $this->calculateStreak(),
            'current_month' => Carbon::create($year, $month, 1)->format('F Y'),
        ];

        return Inertia::render('Workout/Index', [
            'workouts' => $workouts->values(),
            'stats' => $stats,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Workout/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workout_date' => 'required|date',
            'routine_name' => 'required|string|max:255',
            'exercises' => 'nullable|array',
            'exercises.*.name' => 'required|string',
            'exercises.*.sets' => 'nullable|integer',
            'exercises.*.reps' => 'nullable|string',
            'exercises.*.weight' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'intensity' => 'required|in:light,moderate,intense',
        ]);

        // Verificar si ya existe un entrenamiento para esta fecha
        $existingWorkout = WorkoutLog::where('user_id', Auth::id())
            ->where('workout_date', $validated['workout_date'])
            ->first();

        if ($existingWorkout) {
            return back()->withErrors(['workout_date' => 'Ya existe un entrenamiento registrado para esta fecha.']);
        }

        $workout = WorkoutLog::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        return redirect()->route('workouts.index')
            ->with('success', '¡Entrenamiento registrado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkoutLog $workout)
    {
        // Verificar que el workout pertenece al usuario
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Workout/Show', [
            'workout' => $workout,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkoutLog $workout)
    {
        // Verificar que el workout pertenece al usuario
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Workout/Edit', [
            'workout' => $workout,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkoutLog $workout)
    {
        // Verificar que el workout pertenece al usuario
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'workout_date' => 'required|date',
            'routine_name' => 'required|string|max:255',
            'exercises' => 'nullable|array',
            'exercises.*.name' => 'required|string',
            'exercises.*.sets' => 'nullable|integer',
            'exercises.*.reps' => 'nullable|string',
            'exercises.*.weight' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'intensity' => 'required|in:light,moderate,intense',
        ]);

        // Verificar si ya existe otro entrenamiento para esta fecha
        $existingWorkout = WorkoutLog::where('user_id', Auth::id())
            ->where('workout_date', $validated['workout_date'])
            ->where('id', '!=', $workout->id)
            ->first();

        if ($existingWorkout) {
            return back()->withErrors(['workout_date' => 'Ya existe un entrenamiento registrado para esta fecha.']);
        }

        $workout->update($validated);

        return redirect()->route('workouts.index')
            ->with('success', 'Entrenamiento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkoutLog $workout)
    {
        // Verificar que el workout pertenece al usuario
        if ($workout->user_id !== Auth::id()) {
            abort(403);
        }

        $workout->delete();

        return redirect()->route('workouts.index')
            ->with('success', 'Entrenamiento eliminado exitosamente.');
    }

    /**
     * Get workout data for calendar view.
     */
    public function calendar(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $workouts = WorkoutLog::where('user_id', Auth::id())
            ->whereYear('workout_date', $year)
            ->whereMonth('workout_date', $month)
            ->get()
            ->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'date' => $workout->workout_date->format('Y-m-d'),
                    'routine_name' => $workout->routine_name,
                    'intensity' => $workout->intensity,
                    'duration_minutes' => $workout->duration_minutes,
                ];
            });

        return response()->json([
            'workouts' => $workouts,
            'stats' => [
                'total_workouts' => $workouts->count(),
                'total_minutes' => $workouts->sum('duration_minutes'),
            ],
        ]);
    }

    /**
     * Calculate the current workout streak.
     */
    private function calculateStreak(): int
    {
        $workouts = WorkoutLog::where('user_id', Auth::id())
            ->orderBy('workout_date', 'desc')
            ->pluck('workout_date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->values();

        if ($workouts->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = now()->startOfDay();

        // Si el último entrenamiento no es hoy ni ayer, el streak es 0
        $lastWorkout = Carbon::parse($workouts->first());
        if ($lastWorkout->lt($currentDate->copy()->subDay())) {
            return 0;
        }

        foreach ($workouts as $workoutDate) {
            $workout = Carbon::parse($workoutDate);
            
            if ($workout->eq($currentDate) || $workout->eq($currentDate->copy()->subDay())) {
                $streak++;
                $currentDate = $workout->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
