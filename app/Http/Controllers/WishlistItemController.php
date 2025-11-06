<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistItemController extends Controller
{
    public function index(Request $request)
    {
        $query = WishlistItem::where('user_id', Auth::id());

        if ($request->has('obtained')) {
            $query->where('is_obtained', $request->boolean('obtained'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Wishlist/Index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return Inertia::render('Wishlist/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:product,experience,book,movie,other',
            'price' => 'nullable|numeric|min:0',
            'url' => 'nullable|url',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $validated['user_id'] = Auth::id();
        WishlistItem::create($validated);

        return redirect()->route('wishlist.index')
            ->with('success', 'Artículo agregado a tu lista de deseos');
    }

    public function show($id)
    {
        $item = WishlistItem::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Wishlist/Show', ['item' => $item]);
    }

    public function edit($id)
    {
        $item = WishlistItem::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Wishlist/Edit', ['item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = WishlistItem::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:product,experience,book,movie,other',
            'price' => 'nullable|numeric|min:0',
            'url' => 'nullable|url',
            'priority' => 'nullable|in:low,medium,high',
            'is_obtained' => 'boolean',
            'obtained_date' => 'nullable|date',
        ]);

        $item->update($validated);

        return redirect()->route('wishlist.index')
            ->with('success', 'Artículo actualizado');
    }

    public function destroy($id)
    {
        $item = WishlistItem::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();

        return redirect()->route('wishlist.index')
            ->with('success', 'Artículo eliminado');
    }
}
