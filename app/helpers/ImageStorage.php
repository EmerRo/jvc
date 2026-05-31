<?php

require_once __DIR__ . '/ThumbnailHelper.php';

class ImageStorage
{
    const BASE_PATH = 'storage/images/';
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const MAX_SIZE = 5 * 1024 * 1024; // 5MB

    private static function ensureDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private static function extByMime($mime)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        return $map[$mime] ?? 'jpg';
    }

    public static function save($file, $category, $oldFilename = null)
    {
        if (!$file || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Archivo no válido');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error al subir archivo: ' . $file['error']);
        }

        if ($file['size'] > self::MAX_SIZE) {
            throw new RuntimeException('El archivo excede el tamaño máximo de 5MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_TYPES)) {
            throw new RuntimeException('Tipo de archivo no permitido: ' . $mime);
        }

        $ext = self::extByMime($mime);
        $filename = time() . '_' . substr(md5(uniqid('', true)), 0, 10) . '.' . $ext;

        $targetDir = self::BASE_PATH . $category . '/';
        self::ensureDir($targetDir);

        $targetPath = $targetDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Error al mover archivo upload');
        }

        // Generate thumbnail for product/repuesto
        if (in_array($category, ['productos', 'repuestos'])) {
            $thumbDir = self::BASE_PATH . 'thumbnails/' . $category . '/';
            self::ensureDir($thumbDir);
            ThumbnailHelper::generateThumbnail($targetPath, $thumbDir . $filename, 50, 50);
        }

        // Delete old file if replacing
        if ($oldFilename) {
            self::delete($category, $oldFilename);
        }

        return $filename;
    }

    public static function delete($category, $filename)
    {
        if (empty($filename)) return;

        $path = self::BASE_PATH . $category . '/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }

        $thumbPath = self::BASE_PATH . 'thumbnails/' . $category . '/' . $filename;
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }

    public static function serve($category, $filename)
    {
        $path = self::BASE_PATH . $category . '/' . $filename;
        if (!file_exists($path)) {
            self::serveDefault();
            return;
        }

        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

        readfile($path);
        exit;
    }

    public static function serveThumbnail($category, $filename)
    {
        $path = self::BASE_PATH . 'thumbnails/' . $category . '/' . $filename;
        if (!file_exists($path)) {
            self::serveDefault();
            return;
        }

        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: public, max-age=86400');

        readfile($path);
        exit;
    }

    private static function serveDefault()
    {
        header('Content-Type: image/svg+xml');
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="#f0f0f0"/>
            <text x="50" y="55" text-anchor="middle" fill="#999" font-size="12">Sin imagen</text>
        </svg>';
        exit;
    }

    public static function url($category, $filename)
    {
        if (empty($filename)) {
            return URL::to('public/assets/images/no-image.png');
        }
        return URL::to('img/' . $category . '/' . $filename);
    }

    public static function thumbnailUrl($category, $filename)
    {
        if (empty($filename)) {
            return URL::to('public/assets/images/no-image.png');
        }
        return URL::to('img/thumb/' . $category . '/' . $filename);
    }

    public static function getThumbnailFilename($filename)
    {
        return $filename; // Thumbnails share the same filename in thumb subfolder
    }
}
