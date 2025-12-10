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
        $now = Carbon::now()->startOfDay();
        $start = Carbon::parse($this->start_date)->startOfDay();
        
        // Calcular diferencia de días (positivo si start está en el pasado, negativo si está en el futuro)
        $days = $now->diffInDays($start, false);
        return $days;
    }
    
    public function getIsFutureAttribute(): bool
    {
        return Carbon::parse($this->start_date)->isFuture();
    }
    
    public function getDaysRemainingAttribute(): int
    {
        $now = Carbon::now()->startOfDay();
        $start = Carbon::parse($this->start_date)->startOfDay();
        
        if ($start->isFuture()) {
            return $now->diffInDays($start, false);
        }
        
        return 0;
    }
}
