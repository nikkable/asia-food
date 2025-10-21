<?php

namespace context\File\services;

use context\AbstractService;
use context\File\interfaces\ImageResizeServiceInterface;

class ImageResizeService extends AbstractService implements ImageResizeServiceInterface
{
    private const THUMBNAIL_DIR = 'thumbnails';
    private const QUALITY_JPEG = 90;
    private const QUALITY_WEBP = 85;

    public function resize(string $sourceFile, string $targetFile, int $width, int $height, string $mode = 'crop'): bool
    {
        if (!file_exists($sourceFile)) {
            \Yii::error("Source file not found: $sourceFile", __METHOD__);
            return false;
        }

        $imageInfo = getimagesize($sourceFile);
        if (!$imageInfo) {
            \Yii::error("Cannot get image info: $sourceFile", __METHOD__);
            return false;
        }

        $sourceImage = $this->createImageFromFile($sourceFile, $imageInfo[2]);
        if (!$sourceImage) {
            return false;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Создаем целевое изображение
        $targetImage = imagecreatetruecolor($width, $height);
        
        // Сохраняем прозрачность для PNG и GIF
        if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagefill($targetImage, 0, 0, $transparent);
        }

        // Вычисляем координаты и размеры для разных режимов
        [$srcX, $srcY, $srcW, $srcH, $dstX, $dstY, $dstW, $dstH] = $this->calculateDimensions(
            $sourceWidth, $sourceHeight, $width, $height, $mode
        );

        // Копируем и ресайзим
        $result = imagecopyresampled(
            $targetImage, $sourceImage,
            $dstX, $dstY, $srcX, $srcY,
            $dstW, $dstH, $srcW, $srcH
        );

        if (!$result) {
            imagedestroy($sourceImage);
            imagedestroy($targetImage);
            return false;
        }

        // Создаем директорию если не существует
        $targetDir = dirname($targetFile);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Сохраняем изображение
        $success = $this->saveImage($targetImage, $targetFile, $imageInfo[2]);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $success;
    }

    public function getResizedImageUrl(string $originalPath, int $width, int $height, string $mode = 'crop'): string
    {
        // Убираем префикс uploads/ если есть
        $cleanPath = ltrim($originalPath, '/');
        if (strpos($cleanPath, 'uploads/') === 0) {
            $cleanPath = substr($cleanPath, 8);
        }

        $pathInfo = pathinfo($cleanPath);
        $thumbnailName = $pathInfo['filename'] . "_{$width}x{$height}_{$mode}." . $pathInfo['extension'];
        
        $thumbnailRelativePath = self::THUMBNAIL_DIR . '/' . $pathInfo['dirname'] . '/' . $thumbnailName;
        
        // Определяем приложение и формируем URL
        if (\Yii::$app->id === 'app-backend') {
            return '/uploads/' . $thumbnailRelativePath;
        } else {
            $backendUrl = \Yii::$app->params['backendUrl'] ?? 'http://localhost:8080';
            return rtrim($backendUrl, '/') . '/uploads/' . $thumbnailRelativePath;
        }
    }

    public function getImageSize(string $filePath): array|false
    {
        $imageInfo = @getimagesize($filePath);
        return $imageInfo ? [$imageInfo[0], $imageInfo[1]] : false;
    }

    public function thumbnailExists(string $thumbnailPath): bool
    {
        $fullPath = \Yii::getAlias('@backend/web/uploads/' . ltrim($thumbnailPath, '/'));
        return file_exists($fullPath);
    }

    public function deleteThumbnails(string $originalPath): bool
    {
        $cleanPath = ltrim($originalPath, '/');
        if (strpos($cleanPath, 'uploads/') === 0) {
            $cleanPath = substr($cleanPath, 8);
        }

        $pathInfo = pathinfo($cleanPath);
        $thumbnailDir = \Yii::getAlias('@backend/web/uploads/' . self::THUMBNAIL_DIR . '/' . $pathInfo['dirname']);
        
        if (!is_dir($thumbnailDir)) {
            return true;
        }

        $pattern = $pathInfo['filename'] . '_*.' . $pathInfo['extension'];
        $files = glob($thumbnailDir . '/' . $pattern);
        
        $success = true;
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
                \Yii::error("Failed to delete thumbnail: $file", __METHOD__);
            }
        }

        return $success;
    }

    /**
     * Создает изображение из файла
     */
    private function createImageFromFile(string $filePath, int $imageType)
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG => imagecreatefrompng($filePath),
            IMAGETYPE_GIF => imagecreatefromgif($filePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($filePath),
            default => false,
        };
    }

    /**
     * Сохраняет изображение в файл
     */
    private function saveImage($image, string $filePath, int $imageType): bool
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => imagejpeg($image, $filePath, self::QUALITY_JPEG),
            IMAGETYPE_PNG => imagepng($image, $filePath),
            IMAGETYPE_GIF => imagegif($image, $filePath),
            IMAGETYPE_WEBP => imagewebp($image, $filePath, self::QUALITY_WEBP),
            default => false,
        };
    }

    /**
     * Вычисляет координаты и размеры для разных режимов ресайза
     */
    private function calculateDimensions(int $srcW, int $srcH, int $targetW, int $targetH, string $mode): array
    {
        switch ($mode) {
            case 'fit':
                // Вписываем изображение в рамки с сохранением пропорций
                $ratio = min($targetW / $srcW, $targetH / $srcH);
                $newW = (int)($srcW * $ratio);
                $newH = (int)($srcH * $ratio);
                $dstX = (int)(($targetW - $newW) / 2);
                $dstY = (int)(($targetH - $newH) / 2);
                return [0, 0, $srcW, $srcH, $dstX, $dstY, $newW, $newH];

            case 'stretch':
                // Растягиваем на весь размер без сохранения пропорций
                return [0, 0, $srcW, $srcH, 0, 0, $targetW, $targetH];

            case 'crop':
            default:
                // Обрезаем изображение с сохранением пропорций
                $ratio = max($targetW / $srcW, $targetH / $srcH);
                $newW = (int)($targetW / $ratio);
                $newH = (int)($targetH / $ratio);
                $srcX = (int)(($srcW - $newW) / 2);
                $srcY = (int)(($srcH - $newH) / 2);
                return [$srcX, $srcY, $newW, $newH, 0, 0, $targetW, $targetH];
        }
    }
}
