<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Pet;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::where('user_id', Auth::id())
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($request->has('completed')) {
            $query->where('is_completed', $request->boolean('completed'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $todos = $query->get();

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Todos/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'category' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['order'] = Todo::where('user_id', Auth::id())->max('order') + 1;

        $todo = Todo::create($validated);

        return redirect()->route('todos.index')
            ->with('success', 'Tarea creada exitosamente');
    }

    public function show($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('view', $todo);

        return Inertia::render('Todos/Show', [
            'todo' => $todo,
        ]);
    }

    public function edit($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('update', $todo);

        return Inertia::render('Todos/Edit', [
            'todo' => $todo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'category' => 'nullable|string|max:255',
            'is_completed' => 'boolean',
        ]);

        $wasCompleted = $todo->is_completed;
        $todo->update($validated);

        // Si se completó la tarea, dar fichitas a Snoopy y verificar logros
        if (!$wasCompleted && $todo->is_completed) {
            $pet = Pet::firstOrCreate(['user_id' => Auth::id()], [
                'name' => 'Snoopy',
                'happiness' => 50,
                'hunger' => 50,
                'energy' => 50,
                'health' => 100,
                'coins' => 0,
            ]);

            $coins = 5; // 5 fichitas por completar una tarea
            $pet->increment('coins', $coins);
            $pet->increment('happiness', 2);
            
            // Verificar logros
            $achievementService = new AchievementService();
            $unlockedAchievements = $achievementService->checkTodoAchievements(Auth::user());
            
            $message = "¡Tarea completada! Snoopy ganó {$coins} fichitas 🎉";
            if (!empty($unlockedAchievements)) {
                $achievementNames = collect($unlockedAchievements)->pluck('name')->join(', ');
                $message .= " ¡Desbloqueaste logros: {$achievementNames}! 🏆";
            }

            return redirect()->route('todos.index')
                ->with('success', $message);
        }

        return redirect()->route('todos.index')
            ->with('success', 'Tarea actualizada exitosamente');
    }

    public function destroy($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('delete', $todo);

        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Tarea eliminada exitosamente');
    }

    public function toggleComplete($id)
    {
        try {
            $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
            
            // Verificar autorización manualmente si es necesario
            if ($todo->user_id !== Auth::id()) {
                abort(403, 'No autorizado');
            }

            $wasCompleted = $todo->is_completed;
            $todo->is_completed = !$todo->is_completed;
            $todo->save();

            // Si se completó, dar fichitas y verificar logros
            if ($todo->is_completed) {
                $pet = Pet::firstOrCreate(['user_id' => Auth::id()], [
                    'name' => 'Snoopy',
                    'happiness' => 50,
                    'hunger' => 50,
                    'energy' => 50,
                    'health' => 100,
                    'coins' => 0,
                ]);

                $coins = 5;
                $pet->increment('coins', $coins);
                $pet->increment('happiness', 2);
                
                // Verificar logros
                $achievementService = new AchievementService();
                $unlockedAchievements = $achievementService->checkTodoAchievements(Auth::user());
                
                $message = "¡Tarea completada! Snoopy ganó 5 fichitas 🎉";
                if (!empty($unlockedAchievements)) {
                    $achievementNames = collect($unlockedAchievements)->pluck('name')->join(', ');
                    $message .= " ¡Desbloqueaste logros: {$achievementNames}! 🏆";
                }
                
                return back()->with('success', $message);
            }

            return back()->with('success', 'Tarea marcada como pendiente');
        } catch (\Exception $e) {
            \Log::error('Error en toggleComplete: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la tarea. Por favor, intenta de nuevo.');
        }
    }
}
