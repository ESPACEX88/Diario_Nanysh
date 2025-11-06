<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteMeal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'recipe',
        'image_path',
        'type',
        'ingredients',
        'prep_time',
        'cook_time',
        'servings',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'prep_time' => 'integer',
            'cook_time' => 'integer',
            'servings' => 'integer',
            'rating' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
