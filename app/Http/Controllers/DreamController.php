<?php

namespace App\Http\Controllers;

use App\Models\Dream;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DreamController extends Controller
{
    public function index(Request $request)
    {
        $query = Dream::where('user_id', Auth::id())
            ->orderBy('date', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $dreams = $query->get();

        return Inertia::render('Dreams/Index', [
            'dreams' => $dreams,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dreams/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:normal,lucid,nightmare,recurring',
            'tags' => 'nullable|array',
            'mood' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        Dream::create($validated);

        // Dar fichitas por registrar un sueño
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
        
        $coinsEarned = rand(3, 8); // 3-8 fichitas por registrar un sueño
        $pet->coins += $coinsEarned;
        $pet->happiness = min(100, $pet->happiness + 2);
        $pet->save();

        $message = 'Sueño registrado exitosamente.';
        if ($coinsEarned > 0) {
            $message .= " ¡Ganaste {$coinsEarned} fichitas! 💰";
        }

        return redirect()->route('dreams.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $dream = Dream::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Dreams/Show', ['dream' => $dream]);
    }

    public function edit($id)
    {
        $dream = Dream::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Dreams/Edit', ['dream' => $dream]);
    }

    public function update(Request $request, $id)
    {
        $dream = Dream::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:normal,lucid,nightmare,recurring',
            'tags' => 'nullable|array',
            'mood' => 'nullable|string',
        ]);

        $dream->update($validated);

        return redirect()->route('dreams.index')
            ->with('success', 'Sueño actualizado');
    }

    public function destroy($id)
    {
        $dream = Dream::where('user_id', Auth::id())->findOrFail($id);
        $dream->delete();

        return redirect()->route('dreams.index')
            ->with('success', 'Sueño eliminado');
    }
}
