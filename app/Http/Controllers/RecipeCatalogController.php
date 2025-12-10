<?php

namespace App\Http\Controllers;

use App\Models\RecipeCatalog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecipeCatalogController extends Controller
{
    /**
     * Display a listing of recipes from the catalog.
     */
    public function index(Request $request)
    {
        // Mostrar todas las recetas predefinidas (is_predefined = true o null)
        $query = RecipeCatalog::where(function($q) {
            $q->where('is_predefined', true)
              ->orWhereNull('is_predefined');
        });

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by difficulty
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $recipes = $query->orderBy('type')->orderBy('name')->paginate(24);

        // Get unique categories and types for filters
        $categories = RecipeCatalog::where(function($q) {
                $q->where('is_predefined', true)
                  ->orWhereNull('is_predefined');
            })
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $types = RecipeCatalog::where(function($q) {
                $q->where('is_predefined', true)
                  ->orWhereNull('is_predefined');
            })
            ->distinct()
            ->pluck('type')
            ->sort()
            ->values();

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    /**
     * Display the specified recipe.
     */
    public function show(string $id)
    {
        $recipe = RecipeCatalog::findOrFail($id);

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }
}

