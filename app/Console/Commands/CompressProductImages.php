<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompressProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:compress-images {--force : Compress even if already under 50KB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kompres seluruh gambar produk lama di storage menjadi format WebP berukuran < 50KB dan bersihkan file lama';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pemindaian dan kompresi seluruh foto produk lama...');

        $products = Product::whereNotNull('image')->where('image', '!=', '')->get();
        $totalCompressed = 0;
        $totalSavedBytes = 0;

        // 1. Process all products in database
        foreach ($products as $product) {
            $cleanPath = ltrim($product->image, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }

            $storagePath = storage_path('app/public/' . $cleanPath);
            $publicPath = public_path('storage/' . $cleanPath);

            $targetPath = null;
            if (file_exists($storagePath)) {
                $targetPath = $storagePath;
            } elseif (file_exists($publicPath)) {
                $targetPath = $publicPath;
            }

            if (!$targetPath) {
                continue;
            }

            $originalSizeBytes = filesize($targetPath);
            $imageInfo = @getimagesize($targetPath);
            $isSquare = ($imageInfo && isset($imageInfo[0], $imageInfo[1]) && $imageInfo[0] === $imageInfo[1]);

            // Skip if file is already small (< 50KB), 1:1 square ratio and webp (unless --force is specified)
            if ($originalSizeBytes <= 50 * 1024 && $isSquare && !$this->option('force') && str_ends_with(strtolower($cleanPath), '.webp')) {
                continue;
            }

            $imageContent = @file_get_contents($targetPath);
            if (!$imageContent) {
                continue;
            }

            $newRelativePath = ImageOptimizer::compressAndStore($imageContent, 'products');

            if ($newRelativePath) {
                $newStoragePath = storage_path('app/public/' . $newRelativePath);
                $newSizeBytes = file_exists($newStoragePath) ? filesize($newStoragePath) : 0;

                // Delete old uncompressed file
                if ($cleanPath !== $newRelativePath) {
                    if (file_exists($storagePath)) @unlink($storagePath);
                    if (file_exists($publicPath)) @unlink($publicPath);
                }

                $product->image = $newRelativePath;
                $product->save();

                $totalCompressed++;
                $savedBytes = max(0, $originalSizeBytes - $newSizeBytes);
                $totalSavedBytes += $savedBytes;
            }
        }

        // 2. Full Directory Sweep: Purge any old uncompressed .jpg/.png files in products directory
        $directories = [
            storage_path('app/public/products'),
            public_path('storage/products'),
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) continue;

            $files = glob($dir . '/*.{jpg,jpeg,png,bmp,JPG,JPEG,PNG,BMP,gif,GIF}', GLOB_BRACE);
            if (!empty($files)) {
                foreach ($files as $filePath) {
                    $originalSize = filesize($filePath);
                    $filename = basename($filePath);
                    $relPath = 'products/' . $filename;

                    $content = @file_get_contents($filePath);
                    if ($content) {
                        $newRelPath = ImageOptimizer::compressAndStore($content, 'products');
                        if ($newRelPath) {
                            $newStoragePath = storage_path('app/public/' . $newRelPath);
                            $newSizeBytes = file_exists($newStoragePath) ? filesize($newStoragePath) : 0;

                            // Update DB if any product was pointing to this old file
                            Product::where('image', $relPath)
                                ->orWhere('image', '/' . $relPath)
                                ->orWhere('image', 'storage/' . $relPath)
                                ->update(['image' => $newRelPath]);

                            @unlink($filePath);
                            $totalCompressed++;
                            $savedBytes = max(0, $originalSize - $newSizeBytes);
                            $totalSavedBytes += $savedBytes;
                        }
                    } else {
                        @unlink($filePath);
                    }
                }
            }
        }

        $savedMB = round($totalSavedBytes / (1024 * 1024), 2);
        $this->info("Sukses! Berhasil mengompres foto produk dan menghemat {$savedMB} MB ruang penyimpanan.");

        return 0;
    }
}
