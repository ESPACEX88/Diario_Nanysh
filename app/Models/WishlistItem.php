<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'price',
        'url',
        'image_path',
        'priority',
        'is_obtained',
        'obtained_date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_obtained' => 'boolean',
            'obtained_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
