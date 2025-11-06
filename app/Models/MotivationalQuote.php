<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        $dayOfYear = now()->dayOfYear;
        $totalQuotes = self::where('is_active', true)->count();
        
        if ($totalQuotes === 0) {
            return null;
        }
        
        $quoteIndex = $dayOfYear % $totalQuotes;
        return self::where('is_active', true)
            ->skip($quoteIndex)
            ->first();
    }
}
