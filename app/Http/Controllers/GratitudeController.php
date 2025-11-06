<?php

namespace App\Http\Controllers;

use App\Models\Gratitude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class GratitudeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gratitude::where('user_id', Auth::id())
            ->orderBy('date', 'desc');

        // Filter by date range
        if ($request->has('month')) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->has('year')) {
            $query->whereYear('date', $request->year);
        }

        $gratitudes = $query->paginate(30);

        return Inertia::render('Gratitude/Index', [
            'gratitudes' => $gratitudes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if there's already an entry for today
        $todayEntry = Gratitude::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($todayEntry) {
            return redirect()->route('gratitude.edit', $todayEntry->id);
        }

        return Inertia::render('Gratitude/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'item_one' => 'required|string|max:500',
            'item_two' => 'required|string|max:500',
            'item_three' => 'required|string|max:500',
            'reflection' => 'nullable|string',
        ]);

        $gratitude = Gratitude::create([
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'item_one' => $validated['item_one'],
            'item_two' => $validated['item_two'],
            'item_three' => $validated['item_three'],
            'reflection' => $validated['reflection'] ?? null,
        ]);

        return redirect()->route('gratitude.show', $gratitude->id)
            ->with('success', 'Gratitud registrada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gratitude = Gratitude::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Gratitude/Show', [
            'gratitude' => $gratitude,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gratitude = Gratitude::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Gratitude/Edit', [
            'gratitude' => $gratitude,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gratitude = Gratitude::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'item_one' => 'required|string|max:500',
            'item_two' => 'required|string|max:500',
            'item_three' => 'required|string|max:500',
            'reflection' => 'nullable|string',
        ]);

        $gratitude->update($validated);

        return redirect()->route('gratitude.show', $gratitude->id)
            ->with('success', 'Gratitud actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gratitude = Gratitude::where('user_id', Auth::id())
            ->findOrFail($id);

        $gratitude->delete();

        return redirect()->route('gratitude.index')
            ->with('success', 'Gratitud eliminada exitosamente.');
    }
}
