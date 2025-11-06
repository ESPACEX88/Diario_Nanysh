<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'color',
        'points',
        'type',
        'requirement_value',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'requirement_value' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
