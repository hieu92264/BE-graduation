<?php

namespace App\Common\Helpers;

use Buglinjo\LaravelWebp\Facades\Webp;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * store
     *
     * @param mixed $file
     * @param string $fileName
     * @param string $folder
     * @return string
     */
    public static function store(mixed $file, string $fileName, string $folder): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug($fileName) . '_' . time() . '.' . $extension;
        $file->storeAs($folder, $filename);
        return "/uploads/{$folder}/{$filename}";
    }

    /**
     * update
     *
     * @param mixed $file
     * @param string $fileName
     * @param string $folder
     * @param string $oldFile
     * @return string
     */
    public static function update(mixed $file, string $fileName, string $folder, string $oldFile): string
    {
        if (Storage::exists($oldFile)) {
            Storage::delete($oldFile);
        }
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug($fileName) . '_' . time() . '.' . $extension;
        $file->storeAs($folder, $filename);
        return "/uploads/{$folder}/{$filename}";
    }

    /**
     * destroy
     *
     * @param string $imageLink
     * @return void
     */
    public static function destroy(string $imageLink): void
    {
        if (str_starts_with($imageLink, '/')) {
            $imageLink = substr($imageLink, 1);
        }

        if (!is_null($imageLink) && Storage::exists($imageLink)) {
            Storage::delete($imageLink);
            unlink($imageLink);
        }

        if (file_exists($imageLink)) {
            unlink($imageLink);
        }
    }

    /**
     * store webp
     *
     * @param mixed $file
     * @param string $fileName
     * @param string $folder
     * @return string
     * @throws Exception
     */
    public static function storeWebp(mixed $file, string $fileName, string $folder): string
    {
        try {
            $filename = Str::slug($fileName) . '_' . time() . '.webp';

            $directory = public_path('uploads/' . $folder);
            $destinationPath = $directory . '/' . $filename;

            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            Webp::make($file)->save($destinationPath, 80);

            return "uploads/{$folder}/{$filename}";
        } catch (Exception $exception) {
            Log::error("Webp Convert Error: " . $exception->getMessage());
            throw new Exception('Lỗi khi chuyển đổi ảnh sang WebP.');
        }
    }
}
