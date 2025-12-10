<?php

namespace App\Http\Controllers;

use App\Models\DayCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DayCounterController extends Controller
{
    public function index()
    {
        $counters = DayCounter::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('start_date')
            ->get()
            ->map(function ($counter) {
                return [
                    'id' => $counter->id,
                    'name' => $counter->name,
                    'description' => $counter->description,
                    'start_date' => $counter->start_date,
                    'days_count' => $counter->days_count,
                    'icon' => $counter->icon,
                    'color' => $counter->color,
                    'is_future' => $counter->is_future,
                    'days_remaining' => $counter->days_remaining,
                ];
            });

        return Inertia::render('Counters/Index', [
            'counters' => $counters,
        ]);
    }

    public function create()
    {
        return Inertia::render('Counters/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['user_id'] = Auth::id();
        DayCounter::create($validated);

        return redirect()->route('counters.index')
            ->with('success', 'Contador creado exitosamente');
    }

    public function show($id)
    {
        $counter = DayCounter::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Counters/Show', [
            'counter' => [
                'id' => $counter->id,
                'name' => $counter->name,
                'description' => $counter->description,
                'start_date' => $counter->start_date,
                'days_count' => $counter->days_count,
                'icon' => $counter->icon,
                'color' => $counter->color,
            ],
        ]);
    }

    public function edit($id)
    {
        $counter = DayCounter::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Counters/Edit', ['counter' => $counter]);
    }

    public function update(Request $request, $id)
    {
        $counter = DayCounter::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $counter->update($validated);

        return redirect()->route('counters.index')
            ->with('success', 'Contador actualizado');
    }

    public function destroy($id)
    {
        $counter = DayCounter::where('user_id', Auth::id())->findOrFail($id);
        $counter->delete();

        return redirect()->route('counters.index')
            ->with('success', 'Contador eliminado');
    }
}
