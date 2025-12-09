<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\Tag;
use App\Models\Pet;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

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

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by mood
        if ($request->has('mood') && $request->mood) {
            $query->where('mood', $request->mood);
        }

        $entries = $query->paginate(15);

        // Get user's tags for filter
        $userEntryIds = DiaryEntry::where('user_id', Auth::id())->pluck('id');
        $userTags = Tag::whereHas('diaryEntries', function($q) use ($userEntryIds) {
            $q->whereIn('diary_entries.id', $userEntryIds);
        })->orderBy('name')->get();

        return Inertia::render('Diary/Index', [
            'entries' => $entries,
            'tags' => $userTags,
            'filters' => $request->only(['search', 'favorite', 'tag', 'date_from', 'date_to', 'mood']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get user's existing tags
        $userEntryIds = DiaryEntry::where('user_id', Auth::id())->pluck('id');
        $tags = Tag::whereHas('diaryEntries', function($q) use ($userEntryIds) {
            $q->whereIn('diary_entries.id', $userEntryIds);
        })->orderBy('name')->get();

        return Inertia::render('Diary/Create', [
            'tags' => $tags,
        ]);
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

        // Asegurar que la fecha se guarde correctamente (sin problemas de zona horaria)
        // Usar la fecha directamente sin parsear para evitar conversiones de zona horaria
        $date = $validated['date'];
        
        // Validar que sea una fecha válida en formato Y-m-d
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->format('Y-m-d');
        }
        
        $entry = DiaryEntry::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'mood' => $validated['mood'] ?? '😊',
            'date' => $date,
        ]);

        // Attach tags
        if (isset($validated['tags']) && is_array($validated['tags'])) {
            $entry->tags()->sync($validated['tags']);
        }

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

        // Get user's existing tags
        $userEntryIds = DiaryEntry::where('user_id', Auth::id())->pluck('id');
        $tags = Tag::whereHas('diaryEntries', function($q) use ($userEntryIds) {
            $q->whereIn('diary_entries.id', $userEntryIds);
        })->orderBy('name')->get();

        return Inertia::render('Diary/Edit', [
            'entry' => $entry,
            'tags' => $tags,
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

        // Asegurar que la fecha se guarde correctamente (sin problemas de zona horaria)
        $date = $validated['date'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->format('Y-m-d');
        }
        $validated['date'] = $date;

        $entry->update($validated);

        // Sync tags
        if (isset($validated['tags'])) {
            $entry->tags()->sync($validated['tags']);
        } else {
            $entry->tags()->detach();
        }

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
        try {
            $entry = DiaryEntry::where('user_id', Auth::id())
                ->findOrFail($id);

            $entry->is_favorite = !$entry->is_favorite;
            $entry->save();

            $message = $entry->is_favorite 
                ? 'Marcado como favorito.' 
                : 'Eliminado de favoritos.';

            // Si es una petición AJAX/Inertia, devolver JSON
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_favorite' => $entry->is_favorite
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error en toggleFavorite: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar el favorito. Por favor, intenta de nuevo.');
        }
    }
}
