<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'color',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_end_date',
        'send_reminder',
        'reminder_minutes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'recurrence_end_date' => 'date',
            'is_recurring' => 'boolean',
            'send_reminder' => 'boolean',
            'reminder_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
