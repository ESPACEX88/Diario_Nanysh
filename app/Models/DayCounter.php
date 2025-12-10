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
        
        // Si la fecha de inicio es en el futuro, retornar 0
        if ($start->isFuture()) {
            return 0;
        }
        
        // Calcular días transcurridos desde la fecha de inicio (siempre positivo)
        // diffInDays con false retorna negativo si start > now, así que usamos abs
        $days = $now->diffInDays($start, false);
        return abs($days);
    }
}
