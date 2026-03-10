<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageOptimizerController extends Controller
{
    /** Max width/height for resized images */
    private const MAX_DIMENSION = 1920;

    /** Quality for WebP (0-100) */
    private const WEBP_QUALITY = 82;

    /** Quality for JPEG (0-100) */
    private const JPEG_QUALITY = 85;

    /** Cache for 1 year (optimized images are immutable by path+w+h+format) */
    private const CACHE_MAX_AGE = 31536000;

    /**
     * Serve an optimized image: resized and optionally WebP.
     * Query: path (storage path relative to app/public), w, h (optional).
     * Responds with WebP if Accept: image/webp, else original format resized.
     */
    public function __invoke(Request $request): Response|StreamedResponse
    {
        $path = $request->query('path');
        $w = $request->query('w') ? (int) $request->query('w') : null;
        $h = $request->query('h') ? (int) $request->query('h') : null;

        if (empty($path) || (empty($w) && empty($h))) {
            abort(400, 'Missing path or dimensions');
        }

        $path = ltrim(str_replace(['\\', '..'], ['/', ''], $path), '/');
        $basePath = 'app/public/' . $path;
        $fullPath = storage_path($basePath);

        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            abort(404, 'Image not found');
        }

        $realBase = realpath(storage_path('app/public'));
        $realFull = realpath($fullPath);
        if ($realBase === false || $realFull === false || ! str_starts_with($realFull, $realBase . DIRECTORY_SEPARATOR)) {
            abort(403, 'Invalid path');
        }

        $w = $w ? min($w, self::MAX_DIMENSION) : null;
        $h = $h ? min($h, self::MAX_DIMENSION) : null;
        $wantsWebp = $request->accepts('image/webp') && function_exists('imagewebp');
        $sourcePng = ($info = @getimagesize($fullPath)) && ($info[2] ?? 0) === IMAGETYPE_PNG;

        $cachePath = $this->cachePath($path, $w, $h, $wantsWebp, $sourcePng);
        $cacheFull = public_path($cachePath);

        if (is_file($cacheFull) && is_readable($cacheFull)) {
            return $this->sendCachedFile($cacheFull, $wantsWebp);
        }

        $result = $this->optimize($fullPath, $cacheFull, $w, $h, $wantsWebp, $sourcePng);
        if (! $result) {
            abort(500, 'Image processing failed');
        }

        return $this->sendCachedFile($cacheFull, $wantsWebp);
    }

    private function cachePath(string $path, ?int $w, ?int $h, bool $webp, bool $sourcePng = false): string
    {
        $suffix = $webp ? '-webp' : ($sourcePng ? '-png' : '-jpg');
        $key = $path . '-' . ($w ?? '') . 'x' . ($h ?? '') . $suffix;
        $hash = md5($key);
        $dir = 'cache/img/' . substr($hash, 0, 2);
        $ext = $webp ? '.webp' : ($sourcePng ? '.png' : '.jpg');

        return $dir . '/' . $hash . $ext;
    }

    private function sendCachedFile(string $fullPath, bool $webp): Response
    {
        $mime = mime_content_type($fullPath) ?: ($webp ? 'image/webp' : 'image/jpeg');
        $content = file_get_contents($fullPath);

        return response($content, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=' . self::CACHE_MAX_AGE,
            'Expires' => gmdate('D, d M Y H:i:s', time() + self::CACHE_MAX_AGE) . ' GMT',
        ]);
    }

    private function optimize(string $sourcePath, string $destPath, ?int $w, ?int $h, bool $toWebp, bool $sourcePng = false): bool
    {
        $info = @getimagesize($sourcePath);
        if (! $info) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $type = $info[2];

        $img = $this->loadImage($sourcePath, $type);
        if (! $img) {
            return false;
        }

        if ($w !== null || $h !== null) {
            $targetW = $w ?? (int) round($width * ($h / $height));
            $targetH = $h ?? (int) round($height * ($w / $width));
            $targetW = min($targetW, $width);
            $targetH = min($targetH, $height);
            if ($targetW < $width || $targetH < $height) {
                $resized = imagescale($img, $targetW, $targetH === 0 ? -1 : $targetH);
                if ($resized !== false) {
                    imagedestroy($img);
                    $img = $resized;
                }
            }
        }

        $dir = dirname($destPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $saved = false;
        if ($toWebp && function_exists('imagewebp')) {
            $saved = imagewebp($img, $destPath, self::WEBP_QUALITY);
        }
        if (! $saved && $sourcePng && (pathinfo($destPath, PATHINFO_EXTENSION) === 'png')) {
            $saved = imagepng($img, $destPath, 8);
        }
        if (! $saved) {
            $saved = imagejpeg($img, $destPath, self::JPEG_QUALITY);
        }

        imagedestroy($img);

        return $saved;
    }

    private function loadImage(string $path, int $type): \GdImage|false
    {
        return match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }
}
