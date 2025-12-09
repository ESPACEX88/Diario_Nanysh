<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeCatalog extends Model
{
    protected $table = 'recipe_catalog';
    
    protected $fillable = [
        'name',
        'description',
        'recipe',
        'type',
        'ingredients',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'category',
        'calories',
        'is_predefined',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'prep_time' => 'integer',
            'cook_time' => 'integer',
            'servings' => 'integer',
            'calories' => 'integer',
            'is_predefined' => 'boolean',
        ];
    }
}
