<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderBy('is_completed', 'asc')
            ->orderBy('target_date', 'asc')
            ->paginate(20);

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Goals/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'progress_percentage' => $validated['progress_percentage'] ?? 0,
            'is_completed' => false,
        ]);

        return redirect()->route('goals.show', $goal->id)
            ->with('success', 'Meta creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Goals/Show', [
            'goal' => $goal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'is_completed' => 'boolean',
        ]);

        $goal->update($validated);

        // If completed, set completed_at
        if ($validated['is_completed'] ?? false && !$goal->completed_at) {
            $goal->completed_at = now();
            $goal->save();
        } elseif (!($validated['is_completed'] ?? false)) {
            $goal->completed_at = null;
            $goal->save();
        }

        return redirect()->route('goals.show', $goal->id)
            ->with('success', 'Meta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Meta eliminada exitosamente.');
    }
}
