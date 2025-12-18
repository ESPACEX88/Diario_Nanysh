<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

class ImageService
{
    protected UploadApi $uploadApi;
    protected AdminApi $adminApi;

    public function __construct()
    {
        // Configure Cloudinary
        $cloudinaryUrl = config('cloudinary.url');
        
        if (!$cloudinaryUrl) {
            // Fallback to individual credentials
            $cloudName = config('cloudinary.cloud_name');
            $apiKey = config('cloudinary.api_key');
            $apiSecret = config('cloudinary.api_secret');
            
            if ($cloudName && $apiKey && $apiSecret) {
                $cloudinaryUrl = "cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}";
            }
        }
        
        if ($cloudinaryUrl) {
            Configuration::instance($cloudinaryUrl . '?secure=true');
        }
        
        $this->uploadApi = new UploadApi();
        $this->adminApi = new AdminApi();
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
        try {
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
            
            // Generate a unique public_id
            $publicId = $folder . '/' . uniqid() . '_' . time();
            
            // Upload to Cloudinary
            $result = $this->uploadApi->upload($file->getRealPath(), [
                'folder' => $folder,
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image',
                'transformation' => [
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto'
                ]
            ]);

            // The main image URL
            $path = $result['secure_url'];
            
            // Create thumbnail transformation URL
            $thumbnailPath = $this->getThumbnailUrl($result['public_id']);

            return [
                'path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'public_id' => $result['public_id'],
            ];
        } catch (\Exception $e) {
            Log::error('Error en ImageService::store', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);
            throw new \RuntimeException('Error al subir la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Get thumbnail URL for a Cloudinary image.
     *
     * @param string $publicId
     * @return string
     */
    public function getThumbnailUrl(string $publicId): string
    {
        // Cloudinary transformation URL for thumbnail
        $cloudName = config('cloudinary.cloud_name');
        return "https://res.cloudinary.com/{$cloudName}/image/upload/w_300,h_300,c_fill,q_auto,f_auto/{$publicId}";
    }

    /**
     * Delete an image from Cloudinary.
     *
     * @param string $publicIdOrUrl Can be public_id or full URL
     * @return void
     */
    public function delete(string $publicIdOrUrl): void
    {
        try {
            // Extract public_id from URL if needed
            $publicId = $this->extractPublicId($publicIdOrUrl);
            
            if ($publicId) {
                $this->uploadApi->destroy($publicId);
                Log::info('Imagen eliminada de Cloudinary', ['public_id' => $publicId]);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen de Cloudinary', [
                'message' => $e->getMessage(),
                'public_id' => $publicIdOrUrl,
            ]);
        }
    }

    /**
     * Extract public_id from Cloudinary URL.
     *
     * @param string $urlOrPublicId
     * @return string|null
     */
    protected function extractPublicId(string $urlOrPublicId): ?string
    {
        // If it's already a public_id, return it
        if (!str_contains($urlOrPublicId, 'cloudinary.com')) {
            return $urlOrPublicId;
        }

        // Extract from URL
        // Example: https://res.cloudinary.com/cloud/image/upload/v123456/photos/abc123.jpg
        preg_match('/\/upload\/(?:v\d+\/)?(.+)\.\w+$/', $urlOrPublicId, $matches);
        
        return $matches[1] ?? null;
    }

    /**
     * Resize an image (returns new URL with transformation).
     *
     * @param string $publicId
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resize(string $publicId, int $width, int $height): string
    {
        $cloudName = config('cloudinary.cloud_name');
        return "https://res.cloudinary.com/{$cloudName}/image/upload/w_{$width},h_{$height},c_fill,q_auto,f_auto/{$publicId}";
    }
}

