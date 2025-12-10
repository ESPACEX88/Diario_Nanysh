<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Album;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Photo::where('user_id', Auth::id())
            ->with(['album'])
            ->orderBy('taken_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by album
        if ($request->has('album_id') && $request->album_id) {
            $query->where('album_id', $request->album_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $photos = $query->paginate(24);

        // Get user's albums for filter
        $albums = Album::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return Inertia::render('Photos/Index', [
            'photos' => $photos,
            'albums' => $albums,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $albums = Album::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return Inertia::render('Photos/Create', [
            'albums' => $albums,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,gif,webp|max:5120', // 5MB
            'description' => 'nullable|string|max:1000',
            'album_id' => 'nullable|exists:albums,id',
            'taken_at' => 'nullable|date',
        ]);

        try {
            $imageData = $this->imageService->store($validated['photo'], 'photos');

            $photo = Photo::create([
                'user_id' => Auth::id(),
                'album_id' => $validated['album_id'] ?? null,
                'path' => $imageData['path'],
                'thumbnail_path' => $imageData['thumbnail_path'],
                'description' => $validated['description'] ?? null,
                'taken_at' => $validated['taken_at'] ?? now(),
                'photoable_id' => null,
                'photoable_type' => null,
            ]);

            return redirect()->route('photos.index')
                ->with('success', 'Foto subida exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'photo' => 'Error al subir la foto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $photo = Photo::where('user_id', Auth::id())
            ->with(['album'])
            ->findOrFail($id);

        return Inertia::render('Photos/Show', [
            'photo' => $photo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $photo = Photo::where('user_id', Auth::id())
            ->findOrFail($id);

        $albums = Album::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return Inertia::render('Photos/Edit', [
            'photo' => $photo,
            'albums' => $albums,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $photo = Photo::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'description' => 'nullable|string|max:1000',
            'album_id' => 'nullable|exists:albums,id',
            'taken_at' => 'nullable|date',
        ]);

        $photo->update($validated);

        return redirect()->route('photos.index')
            ->with('success', 'Foto actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $photo = Photo::where('user_id', Auth::id())
            ->findOrFail($id);

        // Delete files from storage
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        if ($photo->thumbnail_path && Storage::disk('public')->exists($photo->thumbnail_path)) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }

        $photo->delete();

        return redirect()->route('photos.index')
            ->with('success', 'Foto eliminada exitosamente.');
    }
}
