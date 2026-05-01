<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlideshowImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SlideshowController extends Controller
{
    private const DESKTOP_W = 1600;
    private const DESKTOP_H = 700;
    private const MOBILE_W = 960;
    private const MOBILE_H = 420;
    private const THUMB_W = 480;
    private const THUMB_H = 210;

    public function index()
    {
        $images = SlideshowImage::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.slideshow.index', [
            'images' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'file', 'mimes:jpg,jpeg,jfif,png,webp,tif,heif', 'mimetypes:image/jpeg,image/pjpeg,image/png,image/webp,image/jpg,image/jfif,image/tiff,image/heif,image/heic'],
            'target_width' => ['nullable', 'integer', 'min:240', 'max:4096'],
            'target_height' => ['nullable', 'integer', 'min:180', 'max:4096'],
        ]);

        $files = $validated['photos'] ?? [];
        $success = 0;
        $failures = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            try {
                $this->storeOne($request, $file);
                $success++;
            } catch (\Throwable $e) {
                report($e);
                $failures[] = $file->getClientOriginalName() ?: 'Unknown file';
            }
        }

        Log::info('admin.slideshow.upload', [
            'user_id' => $request->user()?->id,
            'success' => $success,
            'failed' => count($failures),
        ]);

        if ($success === 0) {
            $message = 'No images were uploaded. Please check the file format/size and try again.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withErrors(['photos' => $message]);
        }

        if (count($failures) > 0) {
            $message = 'Uploaded '.$success.' image(s). Some files failed: '.implode(', ', array_slice($failures, 0, 5)).(count($failures) > 5 ? '…' : '');
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'success' => $success,
                    'failed' => $failures,
                ]);
            }

            return redirect()->route('admin.slideshow.index')->with('status', $message);
        }

        $message = 'Uploaded '.$success.' image(s).';
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'success' => $success,
                'failed' => [],
            ]);
        }

        return redirect()->route('admin.slideshow.index')->with('status', $message);
    }

    public function destroy(Request $request, SlideshowImage $slideshow_image)
    {
        $id = $slideshow_image->id;
        $this->deleteImage($slideshow_image);

        Log::info('admin.slideshow.delete', [
            'user_id' => $request->user()?->id,
            'slideshow_image_id' => $id,
        ]);

        return redirect()
            ->route('admin.slideshow.index')
            ->with('status', 'Slideshow image deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $ids = array_values(array_unique($validated['ids']));
        $images = SlideshowImage::query()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            $this->deleteImage($image);
        }

        Log::info('admin.slideshow.bulk_delete', [
            'user_id' => $request->user()?->id,
            'count' => $images->count(),
        ]);

        return redirect()
            ->route('admin.slideshow.index')
            ->with('status', 'Deleted '.$images->count().' slideshow image(s).');
    }

    private function storeOne(Request $request, UploadedFile $file): void
    {
        $data = @file_get_contents($file->getRealPath());
        if (! is_string($data) || $data === '') {
            throw new \RuntimeException('Failed to read upload.');
        }

        $info = @getimagesizefromstring($data);
        if (! is_array($info) || ! isset($info['mime'])) {
            throw new \RuntimeException('Invalid image.');
        }

        $mime = (string) $info['mime'];
        $allowedMimes = ['image/jpeg', 'image/pjpeg', 'image/jpg', 'image/jfif', 'image/png', 'image/webp', 'image/tiff', 'image/heif', 'image/heic'];
        if (! in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Unsupported image format.');
        }

        if (in_array($mime, ['image/tiff', 'image/heif', 'image/heic'], true)) {
            if (! class_exists(\Imagick::class)) {
                throw new \RuntimeException('TIFF/HEIF uploads require the Imagick PHP extension.');
            }

            $this->storeOneViaImagick($request, $file, $mime, $info);
            return;
        }

        $source = @imagecreatefromstring($data);
        if (! $source) {
            throw new \RuntimeException('Failed to decode image.');
        }

        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($source);
        }

        $this->enhance($source);

        $base = (string) Str::uuid();

        $targets = $this->resolveTargets($request);
        $outputExt = $this->outputExtForMime($mime);
        $preserveAlpha = $outputExt === 'png';

        $desktopPath = 'slideshow/'.$base.'.'.$outputExt;

        $desktop = $this->coverCrop($source, $targets['large_w'], $targets['large_h'], $preserveAlpha);

        $desktopBytes = $this->encodeVariant($desktop, $outputExt);

        $disk = Storage::disk('public');

        $originalBytes = (int) $file->getSize();
        $desktopLen = strlen($desktopBytes);

        DB::transaction(function () use (
            $disk,
            $request,
            $file,
            $mime,
            $base,
            $info,
            $desktopPath,
            $desktopBytes,
            $targets
        ) {
            $disk->put($desktopPath, $desktopBytes);

            SlideshowImage::query()->create([
                'uploaded_by' => $request->user()?->id,
                'base_name' => $base,
                'original_name' => (string) $file->getClientOriginalName(),
                'original_mime' => $mime,
                'original_size' => (int) $file->getSize(),
                'original_path' => $desktopPath,
                'desktop_path' => $desktopPath,
                'desktop_size' => (int) ($disk->size($desktopPath) ?? 0),
                'desktop_width' => $targets['large_w'],
                'desktop_height' => $targets['large_h'],
                'mobile_path' => $desktopPath,
                'mobile_size' => (int) ($disk->size($desktopPath) ?? 0),
                'thumb_path' => $desktopPath,
                'thumb_size' => (int) ($disk->size($desktopPath) ?? 0),
            ]);
        });

        imagedestroy($source);
        imagedestroy($desktop);

        Log::info('admin.slideshow.optimize', [
            'user_id' => $request->user()?->id,
            'base_name' => $base,
            'mime' => $mime,
            'original_size' => $originalBytes,
            'desktop_size' => $desktopLen,
            'desktop_ratio' => $originalBytes > 0 ? round($desktopLen / $originalBytes, 4) : null,
            'target' => $targets,
        ]);
    }

    private function storeOneViaImagick(Request $request, UploadedFile $file, string $mime, array $info): void
    {
        $data = @file_get_contents($file->getRealPath());
        if (! is_string($data) || $data === '') {
            throw new \RuntimeException('Failed to read upload.');
        }

        $targets = $this->resolveTargets($request);
        $outputExt = 'jpg';

        $base = (string) Str::uuid();
        $desktopPath = 'slideshow/'.$base.'.'.$outputExt;

        $img = new \Imagick();
        $img->readImageBlob($data);
        if (method_exists($img, 'autoOrient')) {
            $img->autoOrient();
        }
        if (method_exists($img, 'stripImage')) {
            $img->stripImage();
        }

        $desktopBytes = $this->imagickCoverCropEncode($img, $targets['large_w'], $targets['large_h'], $outputExt);

        $disk = Storage::disk('public');

        $originalBytes = (int) $file->getSize();
        $desktopLen = strlen($desktopBytes);

        DB::transaction(function () use (
            $disk,
            $request,
            $file,
            $mime,
            $base,
            $desktopPath,
            $desktopBytes,
            $targets
        ) {
            $disk->put($desktopPath, $desktopBytes);

            SlideshowImage::query()->create([
                'uploaded_by' => $request->user()?->id,
                'base_name' => $base,
                'original_name' => (string) $file->getClientOriginalName(),
                'original_mime' => $mime,
                'original_size' => (int) $file->getSize(),
                'original_path' => $desktopPath,
                'desktop_path' => $desktopPath,
                'desktop_size' => (int) ($disk->size($desktopPath) ?? 0),
                'desktop_width' => $targets['large_w'],
                'desktop_height' => $targets['large_h'],
                'mobile_path' => $desktopPath,
                'mobile_size' => (int) ($disk->size($desktopPath) ?? 0),
                'thumb_path' => $desktopPath,
                'thumb_size' => (int) ($disk->size($desktopPath) ?? 0),
            ]);
        });

        Log::info('admin.slideshow.optimize', [
            'user_id' => $request->user()?->id,
            'base_name' => $base,
            'mime' => $mime,
            'original_size' => $originalBytes,
            'desktop_size' => $desktopLen,
            'desktop_ratio' => $originalBytes > 0 ? round($desktopLen / $originalBytes, 4) : null,
            'target' => $targets,
        ]);
    }

    private function imagickCoverCropEncode(\Imagick $source, int $dstW, int $dstH, string $ext): string
    {
        $img = clone $source;
        $img->setIteratorIndex(0);
        $img->setImageBackgroundColor('black');
        $img->cropThumbnailImage($dstW, $dstH);

        $ext = strtolower(trim($ext));
        if ($ext === 'png') {
            $img->setImageFormat('png');
            $img->setOption('png:compression-level', '6');
        } elseif ($ext === 'webp') {
            $img->setImageFormat('webp');
            $img->setImageCompressionQuality(80);
        } else {
            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(80);
        }

        if (method_exists($img, 'stripImage')) {
            $img->stripImage();
        }

        $bytes = $img->getImagesBlob();
        if (! is_string($bytes) || $bytes === '') {
            throw new \RuntimeException('Image optimization failed.');
        }

        return $bytes;
    }

    private function deleteImage(SlideshowImage $image): void
    {
        $disk = Storage::disk('public');
        $paths = array_values(array_unique([
            (string) $image->original_path,
            (string) $image->desktop_path,
            (string) $image->mobile_path,
            (string) $image->thumb_path,
        ]));

        $image->delete();

        foreach ($paths as $path) {
            if ($path !== '') {
                $disk->delete($path);
            }
        }
    }

    private function coverCrop($source, int $dstW, int $dstH, bool $preserveAlpha = false)
    {
        $srcW = imagesx($source);
        $srcH = imagesy($source);

        if ($srcW <= 0 || $srcH <= 0) {
            $canvas = $this->createCanvas($dstW, $dstH, $preserveAlpha);
            return $canvas;
        }

        $scale = max($dstW / $srcW, $dstH / $srcH);
        $tmpW = max(1, (int) ceil($srcW * $scale));
        $tmpH = max(1, (int) ceil($srcH * $scale));

        $tmp = $this->createCanvas($tmpW, $tmpH, $preserveAlpha);
        imagecopyresampled($tmp, $source, 0, 0, 0, 0, $tmpW, $tmpH, $srcW, $srcH);

        $x = max(0, (int) floor(($tmpW - $dstW) / 2));
        $y = max(0, (int) floor(($tmpH - $dstH) / 2));

        $canvas = $this->createCanvas($dstW, $dstH, $preserveAlpha);
        imagecopy($canvas, $tmp, 0, 0, $x, $y, $dstW, $dstH);

        imagedestroy($tmp);

        return $canvas;
    }

    private function createCanvas(int $w, int $h, bool $preserveAlpha)
    {
        $img = imagecreatetruecolor(max(1, $w), max(1, $h));
        if (! $img) {
            throw new \RuntimeException('Failed to allocate image buffer.');
        }

        if ($preserveAlpha) {
            if (function_exists('imagealphablending')) {
                imagealphablending($img, false);
            }
            if (function_exists('imagesavealpha')) {
                imagesavealpha($img, true);
            }
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefill($img, 0, 0, $transparent);
        } else {
            imagefill($img, 0, 0, imagecolorallocate($img, 0, 0, 0));
        }

        return $img;
    }

    private function enhance($image): void
    {
        if (function_exists('imagefilter')) {
            @imagefilter($image, IMG_FILTER_BRIGHTNESS, 2);
            @imagefilter($image, IMG_FILTER_CONTRAST, -5);
        }
    }

    private function resolveTargets(Request $request): array
    {
        $w = (int) $request->input('target_width', 0);
        $h = (int) $request->input('target_height', 0);

        if ($w < 240 || $h < 180) {
            $w = self::DESKTOP_W;
            $h = self::DESKTOP_H;
        }

        $w = max(240, min(4096, $w));
        $h = max(180, min(4096, $h));

        $ratio = $w > 0 ? ($h / $w) : (self::DESKTOP_H / self::DESKTOP_W);

        $mediumW = (int) round($w * 0.6);
        $mediumW = max(480, min(1400, $mediumW));
        $mediumH = max(1, (int) round($mediumW * $ratio));

        $thumbW = (int) round($w * 0.3);
        $thumbW = max(240, min(640, $thumbW));
        $thumbH = max(1, (int) round($thumbW * $ratio));

        return [
            'large_w' => $w,
            'large_h' => $h,
            'medium_w' => $mediumW,
            'medium_h' => $mediumH,
            'thumb_w' => $thumbW,
            'thumb_h' => $thumbH,
        ];
    }

    private function outputExtForMime(string $mime): string
    {
        $mime = strtolower(trim($mime));
        if ($mime === 'image/png') {
            return 'png';
        }

        if (function_exists('imagewebp')) {
            return 'webp';
        }

        return 'jpg';
    }

    private function encodeVariant($image, string $ext): string
    {
        $ext = strtolower(trim($ext));
        ob_start();

        if ($ext === 'png') {
            imagepng($image, null, 6);
        } elseif ($ext === 'webp' && function_exists('imagewebp')) {
            imagewebp($image, null, 80);
        } else {
            imagejpeg($image, null, 80);
        }

        $result = ob_get_clean();
        $bytes = is_string($result) ? $result : '';
        if ($bytes === '') {
            throw new \RuntimeException('Image optimization failed.');
        }

        return $bytes;
    }
}
