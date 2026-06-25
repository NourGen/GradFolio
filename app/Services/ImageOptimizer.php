<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ImageOptimizer
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize and store a profile picture.
     * Returns the relative path on the public disk.
     */
    public function optimizeProfile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.webp';
        $folder = 'profiles';
        $storagePath = $folder . '/' . $filename;

        // Load image, apply square center crop (400x400), and encode to WebP (80% quality)
        $image = $this->manager->decode($file->getRealPath());
        $image->cover(400, 400);
        $encoded = $image->encode(new WebpEncoder(80));

        // Store to public disk
        Storage::disk('r2')->put($storagePath, (string) $encoded);

        return $storagePath;
    }

    /**
     * Optimize and store a project cover image + its thumbnail.
     * Returns ['path' => ..., 'thumbnail' => ...] relative paths.
     */
    public function optimizeCover(UploadedFile $file): array
    {
        $filename = Str::uuid() . '.webp';
        $folder = 'project-covers';
        $storagePath = $folder . '/' . $filename;
        $thumbStoragePath = $folder . '/thumbnails/' . $filename;

        // Read file twice to prevent graphics resource sharing conflicts
        $image = $this->manager->decode($file->getRealPath());
        $thumbImage = $this->manager->decode($file->getRealPath());

        // 1. Process Cover (1200x675 px - aspect ratio 16:9)
        $image->cover(1200, 675);
        $encoded = $image->encode(new WebpEncoder(80));
        Storage::disk('r2')->put($storagePath, (string) $encoded);

        // 2. Process Cover Thumbnail (400x225 px - aspect ratio 16:9)
        $thumbImage->cover(400, 225);
        $encodedThumb = $thumbImage->encode(new WebpEncoder(80));
        Storage::disk('r2')->put($thumbStoragePath, (string) $encodedThumb);

        return [
            'path' => $storagePath,
            'thumbnail' => $thumbStoragePath,
        ];
    }

    /**
     * Optimize and store a gallery image + its thumbnail.
     * Returns ['path' => ..., 'thumbnail' => ...] relative paths.
     */
    public function optimizeGallery(UploadedFile $file): array
    {
        $filename = Str::uuid() . '.webp';
        $folder = 'project-gallery';
        $storagePath = $folder . '/' . $filename;
        $thumbStoragePath = $folder . '/thumbnails/' . $filename;

        // Read file twice to prevent graphics resource sharing conflicts
        $image = $this->manager->decode($file->getRealPath());
        $thumbImage = $this->manager->decode($file->getRealPath());

        // 1. Process Gallery Image (Max width 1600 px, auto height, preserve aspect ratio)
        if ($image->width() > 1600) {
            $image->scale(width: 1600);
        }
        $encoded = $image->encode(new WebpEncoder(80));
        Storage::disk('r2')->put($storagePath, (string) $encoded);

        // 2. Process Gallery Thumbnail (300x300 px - square center crop)
        $thumbImage->cover(300, 300);
        $encodedThumb = $thumbImage->encode(new WebpEncoder(80));
        Storage::disk('r2')->put($thumbStoragePath, (string) $encodedThumb);

        return [
            'path' => $storagePath,
            'thumbnail' => $thumbStoragePath,
        ];
    }
}
