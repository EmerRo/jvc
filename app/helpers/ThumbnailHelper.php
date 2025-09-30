<?php

class ThumbnailHelper {

    public static function generateThumbnail($sourcePath, $thumbnailPath, $width = 40, $height = 40) {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $sourceImage = null;

        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$sourceImage) {
            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $thumbnail = imagecreatetruecolor($width, $height);

        if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0, 0, 0, 0,
            $width, $height,
            $sourceWidth, $sourceHeight
        );

        $thumbnailDir = dirname($thumbnailPath);
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        $result = false;
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $result = imagejpeg($thumbnail, $thumbnailPath, 85);
                break;
            case 'image/png':
                $result = imagepng($thumbnail, $thumbnailPath, 6);
                break;
            case 'image/gif':
                $result = imagegif($thumbnail, $thumbnailPath);
                break;
            case 'image/webp':
                $result = imagewebp($thumbnail, $thumbnailPath, 85);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return $result;
    }

    public static function getThumbnailPath($imagePath) {
        if (empty($imagePath)) {
            return null;
        }

        $pathInfo = pathinfo($imagePath);
        $thumbnailPath = 'thumbnails/' . $pathInfo['basename'];

        return $thumbnailPath;
    }

    public static function ensureThumbnailExists($imagePath) {
        if (empty($imagePath)) {
            return null;
        }

        // Si la imagen ya está en la carpeta de thumbnails, la devolvemos directamente
        if (strpos($imagePath, 'thumbnails/') !== false) {
            return file_exists($imagePath) ? $imagePath : null;
        }

        $fullImagePath = 'public/img/productos/' . $imagePath;
        $thumbnailPath = self::getThumbnailPath($imagePath);
        $fullThumbnailPath = 'public/img/productos/' . $thumbnailPath;

        if (!file_exists($fullThumbnailPath)) {
            if (file_exists($fullImagePath)) {
                self::generateThumbnail($fullImagePath, $fullThumbnailPath, 50, 50);
            }
        }

        return file_exists($fullThumbnailPath) ? $thumbnailPath : null;
    }
}