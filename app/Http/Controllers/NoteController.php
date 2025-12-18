<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::where('user_id', Auth::id())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Notes/Index', [
            'notes' => $notes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Notes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'has_checklist' => 'boolean',
            'checklist_items' => 'nullable|array',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'category' => $validated['category'] ?? null,
            'color' => $validated['color'] ?? '#fbbf24',
            'has_checklist' => $validated['has_checklist'] ?? false,
            'checklist_items' => $validated['checklist_items'] ?? null,
        ]);

        // Reward coins for writing notes
        $coinsEarned = rand(3, 8); // Random coins between 3-8
        
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
        
        $pet->coins += $coinsEarned;
        $pet->save();

        return redirect()->route('notes.show', $note->id)
            ->with('success', "Nota creada exitosamente. ¡Ganaste {$coinsEarned} fichitas! 💰");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::where('user_id', Auth::id())
            ->with(['tags'])
            ->findOrFail($id);

        return Inertia::render('Notes/Show', [
            'note' => $note,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $note = Note::where('user_id', Auth::id())
            ->findOrFail($id);

        return Inertia::render('Notes/Edit', [
            'note' => $note,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $note = Note::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'is_pinned' => 'boolean',
            'has_checklist' => 'boolean',
            'checklist_items' => 'nullable|array',
        ]);

        $note->update($validated);

        return redirect()->route('notes.show', $note->id)
            ->with('success', 'Nota actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $note = Note::where('user_id', Auth::id())
            ->findOrFail($id);

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Nota eliminada exitosamente.');
    }

    /**
     * Reorder notes.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|array',
            'notes.*.id' => 'required|exists:notes,id',
            'notes.*.order' => 'required|integer',
        ]);

        foreach ($validated['notes'] as $noteData) {
            Note::where('id', $noteData['id'])
                ->where('user_id', Auth::id())
                ->update(['order' => $noteData['order']]);
        }

        return back()->with('success', 'Orden actualizado exitosamente.');
    }
}
