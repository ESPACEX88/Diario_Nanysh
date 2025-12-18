<?php

namespace App\Http\Controllers;

use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MediaItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaItem::where('user_id', Auth::id());

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Media/Index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('Media/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:book,movie,series,music,podcast',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:want,in_progress,completed',
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        $validated['user_id'] = Auth::id();
        
        if ($validated['status'] === 'in_progress') {
            $validated['started_date'] = now();
        }
        
        if ($validated['status'] === 'completed') {
            $validated['completed_date'] = now();
        }

        MediaItem::create($validated);

        return redirect()->route('media.index')
            ->with('success', 'Artículo agregado exitosamente');
    }

    public function show($id)
    {
        $item = MediaItem::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Media/Show', ['item' => $item]);
    }

    public function edit($id)
    {
        $item = MediaItem::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Media/Edit', ['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = MediaItem::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:book,movie,series,music,podcast',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:want,in_progress,completed',
            'rating' => 'nullable|integer|min:1|max:5',
            'review' => 'nullable|string',
            'url' => 'nullable|url',
        ]);

        if ($validated['status'] === 'in_progress' && !$item->started_date) {
            $validated['started_date'] = now();
        }
        
        if ($validated['status'] === 'completed' && !$item->completed_date) {
            $validated['completed_date'] = now();
        }

        $item->update($validated);

        return redirect()->route('media.index')
            ->with('success', 'Artículo actualizado');
    }

    public function destroy($id)
    {
        $item = MediaItem::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();

        return redirect()->route('media.index')
            ->with('success', 'Artículo eliminado');
    }
}
