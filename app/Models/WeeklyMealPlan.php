<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyMealPlan extends Model
{
    protected $fillable = [
        'user_id',
        'week_start_date',
        'meals',
    ];

    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
            'meals' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
