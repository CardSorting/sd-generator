<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class StorageService
{
    private const THUMBNAIL_WIDTH = 400;
    private const THUMBNAIL_HEIGHT = 400;

    public function storeGeneratedImage(string $imageData, int $generationId): array
    {
        $filename = $this->generateFilename($generationId);
        
        // Store original image
        $imagePath = $this->storeOriginalImage($imageData, $filename);
        
        // Create and store thumbnail
        $thumbnailPath = $this->createThumbnail($imageData, $filename);

        return [
            'image' => $imagePath,
            'thumbnail' => $thumbnailPath,
        ];
    }

    private function generateFilename(int $generationId): string
    {
        return sprintf('gen_%d_%s.png', $generationId, uniqid());
    }

    private function storeOriginalImage(string $imageData, string $filename): string
    {
        $path = 'generations/' . $filename;
        Storage::disk('public')->put($path, $imageData);
        return Storage::disk('public')->url($path);
    }

    private function createThumbnail(string $imageData, string $filename): string
    {
        $image = Image::make($imageData);
        
        // Resize image maintaining aspect ratio
        $image->resize(self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Create thumbnail path
        $thumbnailPath = 'generations/thumbnails/' . $filename;
        
        // Store thumbnail
        Storage::disk('public')->put($thumbnailPath, $image->encode());
        
        return Storage::disk('public')->url($thumbnailPath);
    }

    public function deleteGeneratedImage(string $imageUrl, string $thumbnailUrl): void
    {
        // Extract paths from URLs
        $imagePath = $this->getPathFromUrl($imageUrl);
        $thumbnailPath = $this->getPathFromUrl($thumbnailUrl);

        // Delete files if they exist
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }
    }

    private function getPathFromUrl(string $url): string
    {
        return str_replace(Storage::disk('public')->url(''), '', parse_url($url, PHP_URL_PATH));
    }
}
