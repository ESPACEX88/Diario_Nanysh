<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'dark_mode',
        'language',
        'notifications_enabled',
        'notification_preferences',
    ];

    protected function casts(): array
    {
        return [
            'dark_mode' => 'boolean',
            'notifications_enabled' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
