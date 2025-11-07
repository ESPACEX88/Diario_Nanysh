<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::where('user_id', Auth::id());

        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_date', [
                Carbon::parse($request->start),
                Carbon::parse($request->end),
            ]);
        }

        $events = $query->orderBy('start_date')->get();

        return Inertia::render('Events/Index', [
            'events' => $events,
        ]);
    }

    public function create()
    {
        return Inertia::render('Events/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_end_date' => 'nullable|date',
            'send_reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:1',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['start_date'] = Carbon::parse($validated['start_date']);
        if (isset($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date']);
        }

        Event::create($validated);

        // Dar fichitas por crear un evento
        $pet = Pet::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'name' => 'Snoopy',
                'level' => 1,
                'experience' => 0,
                'happiness' => 100,
                'hunger' => 100,
                'energy' => 100,
                'health' => 100,
                'coins' => 0,
            ]
        );
        
        $coinsEarned = rand(2, 6); // 2-6 fichitas por crear un evento
        $pet->coins += $coinsEarned;
        $pet->happiness = min(100, $pet->happiness + 1);
        $pet->save();

        $message = 'Evento creado exitosamente.';
        if ($coinsEarned > 0) {
            $message .= " ¡Ganaste {$coinsEarned} fichitas! 💰";
        }

        return redirect()->route('events.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Events/Show', ['event' => $event]);
    }

    public function edit($id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Events/Edit', ['event' => $event]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_end_date' => 'nullable|date',
            'send_reminder' => 'boolean',
            'reminder_minutes' => 'nullable|integer|min:1',
        ]);

        $validated['start_date'] = Carbon::parse($validated['start_date']);
        if (isset($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date']);
        }

        $event->update($validated);

        return redirect()->route('events.index')
            ->with('success', 'Evento actualizado exitosamente');
    }

    public function destroy($id)
    {
        $event = Event::where('user_id', Auth::id())->findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Evento eliminado exitosamente');
    }
}
