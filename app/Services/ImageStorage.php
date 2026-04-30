<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorage
{
    public function storeImageWithThumb(
        UploadedFile $file,
        string $directory,
        int $maxWidth,
        int $maxHeight,
        int $thumbWidth,
        int $thumbHeight,
    ): array {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagejpeg')) {
            $path = Storage::disk('public')->putFile($directory, $file);

            return [
                'path' => $path,
                'thumb_path' => $path,
            ];
        }

        $data = @file_get_contents($file->getRealPath());
        if (! is_string($data) || $data === '') {
            $path = Storage::disk('public')->putFile($directory, $file);

            return [
                'path' => $path,
                'thumb_path' => $path,
            ];
        }

        $source = @imagecreatefromstring($data);
        if (! $source) {
            $path = Storage::disk('public')->putFile($directory, $file);

            return [
                'path' => $path,
                'thumb_path' => $path,
            ];
        }

        $optimized = $this->resizeToFit($source, $maxWidth, $maxHeight);
        $thumb = $this->resizeToFit($source, $thumbWidth, $thumbHeight);

        $optimizedPath = trim($directory, '/').'/'.Str::uuid()->toString().'.jpg';
        $thumbPath = trim($directory, '/').'/'.Str::uuid()->toString().'_thumb.jpg';

        Storage::disk('public')->put($optimizedPath, $this->encodeJpeg($optimized));
        Storage::disk('public')->put($thumbPath, $this->encodeJpeg($thumb));

        imagedestroy($source);
        imagedestroy($optimized);
        imagedestroy($thumb);

        return [
            'path' => $optimizedPath,
            'thumb_path' => $thumbPath,
        ];
    }

    private function resizeToFit($source, int $maxWidth, int $maxHeight)
    {
        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);

        if ($srcWidth <= 0 || $srcHeight <= 0) {
            $canvas = imagecreatetruecolor($maxWidth, $maxHeight);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

            return $canvas;
        }

        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1);
        $dstWidth = max(1, (int) round($srcWidth * $ratio));
        $dstHeight = max(1, (int) round($srcHeight * $ratio));

        $canvas = imagecreatetruecolor($dstWidth, $dstHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        return $canvas;
    }

    private function encodeJpeg($image, int $quality = 82): string
    {
        ob_start();
        imagejpeg($image, null, $quality);
        $result = ob_get_clean();

        return is_string($result) ? $result : '';
    }
}

