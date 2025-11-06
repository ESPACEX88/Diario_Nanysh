<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CycleTracking extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'phase',
        'cycle_day',
        'symptoms',
        'mood',
        'flow_level',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'cycle_day' => 'integer',
            'symptoms' => 'array',
            'flow_level' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
