<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAchievements;
use App\Models\WishlistItem;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistItemController extends Controller
{
    use HandlesAchievements;

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
            ->paginate(20)
            ->withQueryString();

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
            'url' => 'nullable|url|max:2048',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $validated['user_id'] = Auth::id();
        WishlistItem::create($validated);

        $unlocked = $this->syncAchievements(['wishlist']);

        return redirect()->route('wishlist.index')
            ->with('success', 'Artículo agregado a tu lista de deseos' . $this->achievementMessage($unlocked));
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
            'url' => 'nullable|url|max:2048',
            'priority' => 'nullable|in:low,medium,high',
            'is_obtained' => 'boolean',
            'obtained_date' => 'nullable|date',
        ]);

        $wasObtained = $item->is_obtained;
        $item->update($validated);

        // Si se marcó como obtenido, dar fichitas
        if (!$wasObtained && $item->is_obtained) {
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
            
            $coinsEarned = rand(10, 25); // 10-25 fichitas por obtener un deseo
            $pet->coins += $coinsEarned;
            $pet->happiness = min(100, $pet->happiness + 5);
            $pet->save();

            $unlocked = $this->syncAchievements(['wishlist', 'pet']);

            return redirect()->route('wishlist.index')
                ->with('success', "¡Felicidades por obtener tu deseo! 🎉 Ganaste {$coinsEarned} fichitas! 💰" . $this->achievementMessage($unlocked));
        }

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
