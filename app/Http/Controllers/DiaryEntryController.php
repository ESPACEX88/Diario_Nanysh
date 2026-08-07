<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAchievements;
use App\Models\DiaryEntry;
use App\Models\Tag;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DiaryEntryController extends Controller
{
    use HandlesAchievements;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DiaryEntry::where('user_id', Auth::id())
            ->with(['tags'])
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

        $userTags = Tag::whereHas('diaryEntries', function ($q) {
            $q->where('user_id', Auth::id());
        })->orderBy('name')->get(['id', 'name', 'color']);

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
        $tags = Tag::whereHas('diaryEntries', function ($q) {
            $q->where('user_id', Auth::id());
        })->orderBy('name')->get(['id', 'name', 'color']);

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
            'title' => 'required|string|min:1|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:10',
            'date' => 'required|date',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        // Asegurar que la fecha se guarde correctamente (sin problemas de zona horaria)
        $date = $validated['date'];
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->format('Y-m-d');
        }

        $entry = DiaryEntry::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'mood' => $validated['mood'] ?? '😊',
            'date' => $date,
        ]);

        if (! empty($validated['tags'])) {
            $entry->tags()->sync($validated['tags']);
        }

        // Reward coins for happy moods
        $happyMoods = ['😊', '😍', '😎', '🥳', '😌', '💖', '✨', '🌟', '💕', '🎉', '🌈', '🦋', '🌸', '🌺', '🌻', '🌷', '🌼', '💐', '🎀', '🎁', '🎈', '🎊', '💝', '💗', '💓', '💞', '💟', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🤎', '🖤', '💯', '🔥', '⭐', '🌟', '💫', '✨', '☀️', '🌙'];

        $mood = $validated['mood'] ?? '😊';
        $coinsEarned = 0;

        if (in_array($mood, $happyMoods, true)) {
            $coinsEarned = rand(5, 15);

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

        $unlockedAchievements = $this->syncAchievements(['diary', 'pet']);
        $this->forgetDashboardCache();

        $message = 'Entrada del diario creada exitosamente.';
        if ($coinsEarned > 0) {
            $message .= " ¡Ganaste {$coinsEarned} fichitas! 💰";
        }

        if (! empty($unlockedAchievements)) {
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

        $tags = Tag::whereHas('diaryEntries', function ($q) {
            $q->where('user_id', Auth::id());
        })->orderBy('name')->get(['id', 'name', 'color']);

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
            'title' => 'required|string|min:1|max:255',
            'content' => 'required|string',
            'mood' => 'nullable|string|max:10',
            'date' => 'required|date',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        // Asegurar que la fecha se guarde correctamente (sin problemas de zona horaria)
        $date = $validated['date'];
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->format('Y-m-d');
        }
        $validated['date'] = $date;

        $entry->update(collect($validated)->except('tags')->all());

        // Sync tags
        if (array_key_exists('tags', $validated)) {
            $entry->tags()->sync($validated['tags'] ?? []);
        }

        $unlocked = $this->syncAchievements(['diary']);
        $this->forgetDashboardCache();
        $message = 'Entrada del diario actualizada exitosamente.' . $this->achievementMessage($unlocked);

        return redirect()->route('diary.show', $entry->id)
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = DiaryEntry::where('user_id', Auth::id())
            ->findOrFail($id);

        $entry->delete();
        $this->forgetDashboardCache();

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

            $entry->is_favorite = ! $entry->is_favorite;
            $entry->save();

            $message = $entry->is_favorite
                ? 'Marcado como favorito.'
                : 'Eliminado de favoritos.';

            $unlocked = $this->syncAchievements(['diary']);
            $this->forgetDashboardCache();
            $message .= $this->achievementMessage($unlocked);

            // Si es una petición AJAX/Inertia, devolver JSON
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_favorite' => $entry->is_favorite,
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error en toggleFavorite: ' . $e->getMessage());

            return back()->with('error', 'Error al actualizar el favorito. Por favor, intenta de nuevo.');
        }
    }

    private function forgetDashboardCache(): void
    {
        $userId = Auth::id();
        \Illuminate\Support\Facades\Cache::forget("dashboard.stats.{$userId}");
        \Illuminate\Support\Facades\Cache::forget("dashboard.week.{$userId}");
        \Illuminate\Support\Facades\Cache::forget("statistics_user_{$userId}");
    }
}
