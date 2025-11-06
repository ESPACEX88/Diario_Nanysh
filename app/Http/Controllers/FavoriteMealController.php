<?php

namespace App\Http\Controllers;

use App\Models\FavoriteMeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteMealController extends Controller
{
    public function index(Request $request)
    {
        $query = FavoriteMeal::where('user_id', Auth::id());

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $meals = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Meals/Index', [
            'meals' => $meals,
        ]);
    }

    public function create()
    {
        return Inertia::render('Meals/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recipe' => 'nullable|string',
            'type' => 'required|in:breakfast,lunch,dinner,snack,dessert,drink',
            'ingredients' => 'nullable|array',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $validated['user_id'] = Auth::id();
        FavoriteMeal::create($validated);

        return redirect()->route('meals.index')
            ->with('success', 'Comida favorita agregada');
    }

    public function show($id)
    {
        $meal = FavoriteMeal::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Meals/Show', ['meal' => $meal]);
    }

    public function edit($id)
    {
        $meal = FavoriteMeal::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Meals/Edit', ['meal' => $meal]);
    }

    public function update(Request $request, $id)
    {
        $meal = FavoriteMeal::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recipe' => 'nullable|string',
            'type' => 'required|in:breakfast,lunch,dinner,snack,dessert,drink',
            'ingredients' => 'nullable|array',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:1',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $meal->update($validated);

        return redirect()->route('meals.index')
            ->with('success', 'Comida favorita actualizada');
    }

    public function destroy($id)
    {
        $meal = FavoriteMeal::where('user_id', Auth::id())->findOrFail($id);
        $meal->delete();

        return redirect()->route('meals.index')
            ->with('success', 'Comida favorita eliminada');
    }
}
