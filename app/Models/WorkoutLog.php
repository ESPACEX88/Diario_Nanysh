<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutLog extends Model
{
    protected $fillable = [
        'user_id',
        'workout_date',
        'routine_name',
        'exercises',
        'duration_minutes',
        'notes',
        'intensity',
    ];

    protected function casts(): array
    {
        return [
            'workout_date' => 'date',
            'exercises' => 'array',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the user that owns the workout log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por rango de fechas.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('workout_date', [$startDate, $endDate]);
    }

    /**
     * Scope para obtener entrenamientos del mes actual.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('workout_date', now()->year)
                     ->whereMonth('workout_date', now()->month);
    }
}
