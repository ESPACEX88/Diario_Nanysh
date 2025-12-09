<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TagController extends Controller
{
    /**
     * Get all tags for autocomplete
     */
    public function index(Request $request)
    {
        $query = Tag::query();

        // Search tags
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $tags = $query->orderBy('name')->get();

        return response()->json($tags);
    }

    /**
     * Get tags used by current user's diary entries
     */
    public function userTags()
    {
        $userEntryIds = DiaryEntry::where('user_id', Auth::id())->pluck('id');
        
        $tags = Tag::whereHas('diaryEntries', function($query) use ($userEntryIds) {
            $query->whereIn('diary_entries.id', $userEntryIds);
        })->withCount(['diaryEntries' => function($query) use ($userEntryIds) {
            $query->whereIn('diary_entries.id', $userEntryIds);
        }])->orderBy('name')->get();

        return response()->json($tags);
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? $this->generateRandomColor(),
        ]);

        return response()->json($tag);
    }

    /**
     * Update tag
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag->update($validated);

        return response()->json($tag);
    }

    /**
     * Delete tag
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Generate random color for tag
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#EC4899', '#F472B6', '#FB7185', '#F87171',
            '#F59E0B', '#EAB308', '#84CC16', '#22C55E',
            '#10B981', '#14B8A6', '#06B6D4', '#3B82F6',
            '#6366F1', '#8B5CF6', '#A855F7', '#D946EF',
        ];
        return $colors[array_rand($colors)];
    }
}

