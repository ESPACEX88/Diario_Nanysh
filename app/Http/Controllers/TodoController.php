<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAchievements;
use App\Models\Todo;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TodoController extends Controller
{
    use HandlesAchievements;

    public function index(Request $request)
    {
        $query = Todo::where('user_id', Auth::id())
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('completed')) {
            $query->where('is_completed', $request->boolean('completed'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $todos = $query->paginate(30)->withQueryString();

        $categories = Todo::where('user_id', Auth::id())
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'categories' => $categories,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'completed' => $request->input('completed'),
            ],
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

        $unlocked = $this->syncAchievements(['todo']);

        return redirect()->route('todos.index')
            ->with('success', 'Tarea creada exitosamente' . $this->achievementMessage($unlocked));
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
            $unlockedAchievements = $this->syncAchievements(['todo', 'pet']);
            
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
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para realizar esta acción.');
        }

        // Buscar la tarea solo del usuario autenticado
        $todo = Todo::where('user_id', $user->id)->find($id);
        
        if (!$todo) {
            return redirect()->route('todos.index')
                ->with('error', 'La tarea no existe o no tienes permiso para eliminarla.');
        }

        // Verificar autorización explícitamente
        if ($todo->user_id !== $user->id) {
            return redirect()->route('todos.index')
                ->with('error', 'No tienes permiso para eliminar esta tarea.');
        }

        try {
            // Intentar eliminar la tarea
            $deleted = $todo->delete();
            
            if (!$deleted) {
                return redirect()->route('todos.index')
                    ->with('error', 'No se pudo eliminar la tarea. Por favor, intenta de nuevo.');
            }

            return redirect()->route('todos.index')
                ->with('success', 'Tarea eliminada exitosamente.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Error de base de datos
            return redirect()->route('todos.index')
                ->with('error', 'Error de base de datos al eliminar la tarea. Por favor, intenta de nuevo.');
        } catch (\Exception $e) {
            // Cualquier otro error
            return redirect()->route('todos.index')
                ->with('error', 'Ocurrió un error inesperado. Por favor, intenta de nuevo.');
        }
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
                $unlockedAchievements = $this->syncAchievements(['todo', 'pet']);
                
                $message = "¡Tarea completada! Snoopy ganó 5 fichitas 🎉";
                if (!empty($unlockedAchievements)) {
                    $achievementNames = collect($unlockedAchievements)->pluck('name')->join(', ');
                    $message .= " ¡Desbloqueaste logros: {$achievementNames}! 🏆";
                }
                
                return back()->with('success', $message);
            }

            return back()->with('success', 'Tarea marcada como pendiente');
        } catch (\Exception $e) {
            Log::error('Error en toggleComplete: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la tarea. Por favor, intenta de nuevo.');
        }
    }
}
