<?php

namespace App\Utils;

use Intervention\Image\Facades\Image;

class ImageUtils
{
    public static function createThumbnail(string $imageData, int $width, int $height): string
    {
        $image = Image::make($imageData);
        
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $image->encode()->__toString();
    }

    public static function optimizeImage(string $imageData, int $quality = 85): string
    {
        $image = Image::make($imageData);
        return $image->encode('jpg', $quality)->__toString();
    }

    public static function getImageDimensions(string $imageData): array
    {
        $image = Image::make($imageData);
        return [
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }

    public static function validateImageDimensions(int $width, int $height): bool
    {
        $minDimension = 64;
        $maxDimension = 2048;
        
        return $width >= $minDimension && 
               $width <= $maxDimension && 
               $height >= $minDimension && 
               $height <= $maxDimension;
    }

    public static function calculateAspectRatio(int $width, int $height): float
    {
        return $width / $height;
    }

    public static function getSupportedAspectRatios(): array
    {
        return [
            '1:1' => ['width' => 512, 'height' => 512],
            '4:3' => ['width' => 640, 'height' => 480],
            '16:9' => ['width' => 704, 'height' => 396],
            'portrait' => ['width' => 384, 'height' => 640],
            'landscape' => ['width' => 640, 'height' => 384],
        ];
    }
}
