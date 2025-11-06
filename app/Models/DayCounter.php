<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DayCounter extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'start_date',
        'icon',
        'color',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDaysCountAttribute(): int
    {
        return Carbon::now()->diffInDays($this->start_date);
    }
}
