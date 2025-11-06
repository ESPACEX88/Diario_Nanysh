<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'frequency',
        'color',
        'icon',
        'reminder_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'reminder_time' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the habit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all habit logs for this habit.
     */
    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }
}
