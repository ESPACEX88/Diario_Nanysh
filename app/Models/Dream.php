<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dream extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'date',
        'type',
        'tags',
        'mood',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'tags' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
