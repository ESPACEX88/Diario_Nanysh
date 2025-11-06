<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Get all diary entries that have this tag.
     */
    public function diaryEntries(): MorphToMany
    {
        return $this->morphedByMany(DiaryEntry::class, 'taggable');
    }

    /**
     * Get all notes that have this tag.
     */
    public function notes(): MorphToMany
    {
        return $this->morphedByMany(Note::class, 'taggable');
    }
}
