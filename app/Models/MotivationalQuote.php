<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MotivationalQuote extends Model
{
    protected $fillable = [
        'quote',
        'author',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function getDailyQuote(): ?self
    {
        $cacheKey = 'motivational_quote.daily_id.' . now()->toDateString();

        $quoteId = Cache::remember($cacheKey, now()->endOfDay(), function () {
            $dayOfYear = now()->dayOfYear;
            $totalQuotes = self::where('is_active', true)->count();

            if ($totalQuotes === 0) {
                return null;
            }

            $quoteIndex = $dayOfYear % $totalQuotes;

            return self::where('is_active', true)
                ->skip($quoteIndex)
                ->value('id');
        });

        return $quoteId ? self::find($quoteId) : null;
    }
}
