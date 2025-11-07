<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'level',
        'experience',
        'happiness',
        'hunger',
        'energy',
        'health',
        'coins',
        'last_fed_at',
        'last_played_at',
        'last_cared_at',
        'last_minigame_at',
    ];

    protected function casts(): array
    {
        return [
            'last_fed_at' => 'datetime',
            'last_played_at' => 'datetime',
            'last_cared_at' => 'datetime',
            'last_minigame_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the pet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pet's mood based on stats.
     */
    public function getMoodAttribute(): string
    {
        $avg = ($this->happiness + $this->hunger + $this->energy + $this->health) / 4;
        
        if ($avg >= 80) return 'happy';
        if ($avg >= 60) return 'content';
        if ($avg >= 40) return 'sad';
        return 'sick';
    }

    /**
     * Add experience and check for level up.
     */
    public function addExperience(int $amount): bool
    {
        $this->experience += $amount;
        $expNeeded = $this->level * 100;
        
        if ($this->experience >= $expNeeded) {
            $this->level++;
            $this->experience = 0;
            $this->health = min(100, $this->health + 10);
            $this->coins += 50;
            return true; // Leveled up!
        }
        
        return false;
    }

    /**
     * Decrease stats over time (called periodically).
     */
    public function decreaseStats(): void
    {
        // Decrease hunger every hour
        if ($this->last_fed_at && $this->last_fed_at->diffInHours(now()) >= 1) {
            $this->hunger = max(0, $this->hunger - 5);
        }
        
        // Decrease happiness if hunger is low
        if ($this->hunger < 30) {
            $this->happiness = max(0, $this->happiness - 2);
        }
        
        // Decrease energy over time
        if ($this->last_played_at && $this->last_played_at->diffInHours(now()) >= 2) {
            $this->energy = max(0, $this->energy - 3);
        }
        
        // Decrease health if other stats are very low
        if ($this->happiness < 20 || $this->hunger < 20) {
            $this->health = max(0, $this->health - 1);
        }
    }
}
