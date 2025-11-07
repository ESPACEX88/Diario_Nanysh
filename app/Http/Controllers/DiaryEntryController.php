<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\Pet;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DiaryEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DiaryEntry::where('user_id', Auth::id())
            ->with(['tags', 'photos'])
            ->orderBy('date', 'desc');

        // Filter by favorite
        if ($request->has('favorite') && $request->favorite) {
            $query->where('is_favorite', true);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $entries = $query->paginate(15);

        return Inertia::render('Diary/Index', [
            'entries' => $entries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Diary/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:10',
            'date' => 'required|date',
        ]);

        $entry = DiaryEntry::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'mood' => $validated['mood'] ?? '😊',
            'date' => $validated['date'],
        ]);

        // Reward coins for happy moods
        $happyMoods = ['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙', '⭐', '🌟', '💫', '✨', '☀️', '🌙', '⭐', '🌟', '💫', '✨', '☀️', '🌙'];
        $sadMoods = ['😢', '😡', '😰', '😨', '😭', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '😤', '😠', '😦', '😧', '😨', '😰', '😱', '😳', '😵', '😶', '😐', '😑', '😯', '😦', '😧', '😨', '😰', '😱', '😳', '😵', '😶', '😐', '😑', '😯'];
        
        $mood = $validated['mood'] ?? '😊';
        $coinsEarned = 0;
        
        if (in_array($mood, $happyMoods)) {
            // Give coins for happy moods
            $coinsEarned = rand(5, 15); // Random coins between 5-15
            
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
        }

        // Verificar logros
        $achievementService = new AchievementService();
        $unlockedAchievements = $achievementService->checkDiaryAchievements(Auth::user());
        
        $message = 'Entrada del diario creada exitosamente.';
        if ($coinsEarned > 0) {
            $message .= " ¡Ganaste {$coinsEarned} fichitas! 💰";
        }
        
        if (!empty($unlockedAchievements)) {
            $achievementNames = collect($unlockedAchievements)->pluck('name')->join(', ');
            $message .= " ¡Desbloqueaste logros: {$achievementNames}! 🏆";
        }

        return redirect()->route('diary.show', $entry->id)
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->with(['tags', 'photos'])
            ->findOrFail($id);

        return Inertia::render('Diary/Show', [
            'entry' => $entry,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->with(['tags', 'photos'])
            ->findOrFail($id);

        return Inertia::render('Diary/Edit', [
            'entry' => $entry,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:10',
            'date' => 'required|date',
        ]);

        $entry->update($validated);

        return redirect()->route('diary.show', $entry->id)
            ->with('success', 'Entrada del diario actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->findOrFail($id);

        $entry->delete();

        return redirect()->route('diary.index')
            ->with('success', 'Entrada del diario eliminada exitosamente.');
    }

    /**
     * Toggle favorite status.
     */
    public function toggleFavorite(string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->findOrFail($id);

        $entry->is_favorite = !$entry->is_favorite;
        $entry->save();

        return back()->with('success', $entry->is_favorite 
            ? 'Marcado como favorito.' 
            : 'Eliminado de favoritos.');
    }
}
