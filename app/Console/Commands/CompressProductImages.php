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
    protected $description = 'Kompres seluruh gambar produk lama di storage menjadi format WebP berukuran < 50KB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pemindaian dan kompresi seluruh foto produk lama...');

        $products = Product::whereNotNull('image')->where('image', '!=', '')->get();
        if ($products->isEmpty()) {
            $this->warn('Tidak ada produk dengan gambar yang ditemukan.');
            return 0;
        }

        $totalCompressed = 0;
        $totalSavedBytes = 0;

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $cleanPath = ltrim($product->image, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }

            // Find physical path
            $storagePath = storage_path('app/public/' . $cleanPath);
            $publicPath = public_path('storage/' . $cleanPath);

            $targetPath = null;
            if (file_exists($storagePath)) {
                $targetPath = $storagePath;
            } elseif (file_exists($publicPath)) {
                $targetPath = $publicPath;
            }

            if (!$targetPath) {
                $bar->advance();
                continue;
            }

            $originalSizeBytes = filesize($targetPath);

            // Skip if file is already small (< 50KB) unless --force is used
            if ($originalSizeBytes <= 50 * 1024 && !$this->option('force') && str_ends_with(strtolower($cleanPath), '.webp')) {
                $bar->advance();
                continue;
            }

            $imageContent = @file_get_contents($targetPath);
            if (!$imageContent) {
                $bar->advance();
                continue;
            }

            // Compress using ImageOptimizer
            $newRelativePath = ImageOptimizer::compressAndStore($imageContent, 'products');

            if ($newRelativePath) {
                $newStoragePath = storage_path('app/public/' . $newRelativePath);
                $newSizeBytes = file_exists($newStoragePath) ? filesize($newStoragePath) : 0;

                // Delete old physical files if path changed
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

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $savedMB = round($totalSavedBytes / (1024 * 1024), 2);
        $this->info("✅ Sukses! Berhasil mengompres {$totalCompressed} gambar produk.");
        $this->info("💾 Total ruang penyimpanan server yang dihemat: {$savedMB} MB.");

        return 0;
    }
}
