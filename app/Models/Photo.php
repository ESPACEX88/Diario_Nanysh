<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'album_id',
        'path',
        'thumbnail_path',
        'cloudinary_public_id',
        'description',
        'taken_at',
        'photoable_id',
        'photoable_type',
    ];

    protected $appends = [
        'full_url',
        'thumbnail_url',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
        ];
    }

    /**
     * Get the full URL for the photo.
     * Handles both Cloudinary URLs and local storage paths.
     */
    public function getFullUrlAttribute(): string
    {
        // Si ya es una URL completa de Cloudinary, devolverla tal cual
        if (str_starts_with($this->path, 'https://')) {
            return $this->path;
        }
        
        // Si es una ruta local, construir la URL
        return asset('storage/' . $this->path);
    }

    /**
     * Get the full URL for the thumbnail.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->thumbnail_path) {
            return $this->getFullUrlAttribute();
        }

        // Si ya es una URL completa de Cloudinary, devolverla tal cual
        if (str_starts_with($this->thumbnail_path, 'https://')) {
            return $this->thumbnail_path;
        }
        
        // Si es una ruta local, construir la URL
        return asset('storage/' . $this->thumbnail_path);
    }

    /**
     * Get the user that owns the photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the album that contains the photo.
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Get the parent photoable model (diary entry).
     */
    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }
}
