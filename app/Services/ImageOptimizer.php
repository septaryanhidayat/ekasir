<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Compress and store product image (max dimension 600px, WebP format, target < 50KB).
     * Accepts UploadedFile or Base64 string.
     */
    public static function compressAndStore($source, string $folder = 'products'): ?string
    {
        if (empty($source)) {
            return null;
        }

        @ini_set('memory_limit', '256M');

        $imageString = null;

        if ($source instanceof UploadedFile) {
            $imageString = file_get_contents($source->getRealPath());
        } elseif (is_string($source)) {
            if (str_starts_with($source, 'data:image')) {
                $base64Parts = explode(',', $source);
                $imageString = base64_decode(end($base64Parts));
            } else {
                $imageString = $source;
            }
        }

        if (empty($imageString)) {
            return null;
        }

        // Try creating GD image resource
        $img = @imagecreatefromstring($imageString);
        if (!$img && function_exists('imagecreatefromwebp')) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'webp_');
            if ($tmpFile) {
                file_put_contents($tmpFile, $imageString);
                $img = @imagecreatefromwebp($tmpFile);
                @unlink($tmpFile);
            }
        }

        if (!$img) {
            if ($source instanceof UploadedFile) {
                return $source->store($folder, 'public');
            }
            return null;
        }

        $width = imagesx($img);
        $height = imagesy($img);

        // 1:1 Square Center Crop
        $cropSize = min($width, $height);
        $srcX = (int) max(0, round(($width - $cropSize) / 2));
        $srcY = (int) max(0, round(($height - $cropSize) / 2));

        $targetSize = min(600, $cropSize);

        $squareImg = imagecreatetruecolor($targetSize, $targetSize);
        imagealphablending($squareImg, false);
        imagesavealpha($squareImg, true);

        imagecopyresampled($squareImg, $img, 0, 0, $srcX, $srcY, $targetSize, $targetSize, $cropSize, $cropSize);
        imagedestroy($img);
        $img = $squareImg;

        // Capture compressed buffer
        ob_start();
        if (function_exists('imagewebp')) {
            imagewebp($img, null, 75);
            $extension = 'webp';
        } else {
            imagejpeg($img, null, 75);
            $extension = 'jpg';
        }
        $compressedContent = ob_get_clean();
        imagedestroy($img);

        $filename = $folder . '/' . Str::random(40) . '.' . $extension;
        Storage::disk('public')->put($filename, $compressedContent);

        // Failsafe: Ensure file exists directly in public/storage if symlink is not linked
        try {
            $publicTargetDir = public_path('storage/' . $folder);
            if (!file_exists($publicTargetDir)) {
                @mkdir($publicTargetDir, 0777, true);
            }
            @file_put_contents(public_path('storage/' . $filename), $compressedContent);
        } catch (\Throwable $e) {
            // Ignore failsafe write errors
        }

        return $filename;
    }
}
