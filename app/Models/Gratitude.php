<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gratitude extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'item_one',
        'item_two',
        'item_three',
        'reflection',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the user that owns the gratitude entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
