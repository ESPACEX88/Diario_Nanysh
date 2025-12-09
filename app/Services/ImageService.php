<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Store an uploaded image and create a thumbnail.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return array ['path' => string, 'thumbnail_path' => string]
     */
    public function store(UploadedFile $file, string $folder = 'photos'): array
    {
        // Validar tipo de archivo
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Tipo de archivo no permitido. Solo se permiten imágenes (JPEG, PNG, GIF, WebP).');
        }
        
        // Validar tamaño (5MB máximo)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('El archivo es demasiado grande. El tamaño máximo es 5MB.');
        }
        
        $path = $file->store($folder, 'public');
        $fullPath = storage_path('app/public/' . $path);

        // Create thumbnail
        $thumbnailPath = $this->createThumbnail($fullPath, $folder);

        return [
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    /**
     * Create a thumbnail from an image.
     *
     * @param string $imagePath
     * @param string $folder
     * @return string
     */
    public function createThumbnail(string $imagePath, string $folder = 'photos'): string
    {
        $image = $this->imageManager->read($imagePath);
        $image->scale(width: 300, height: 300);

        $thumbnailPath = str_replace($folder, 'thumbnails', $imagePath);
        $thumbnailDir = dirname($thumbnailPath);

        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        $image->save($thumbnailPath);

        return str_replace(storage_path('app/public/'), '', $thumbnailPath);
    }

    /**
     * Resize an image.
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @return void
     */
    public function resize(string $path, int $width, int $height): void
    {
        $fullPath = storage_path('app/public/' . $path);
        $image = $this->imageManager->read($fullPath);
        $image->scale(width: $width, height: $height);
        $image->save($fullPath);
    }

    /**
     * Delete an image and its thumbnail.
     *
     * @param string $path
     * @return void
     */
    public function delete(string $path): void
    {
        Storage::disk('public')->delete($path);

        $thumbnailPath = str_replace('photos', 'thumbnails', $path);
        Storage::disk('public')->delete($thumbnailPath);
    }
}

