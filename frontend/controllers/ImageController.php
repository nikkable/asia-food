<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use context\File\interfaces\ImageResizeServiceInterface;

/**
 * Контроллер для динамической генерации изображений
 */
class ImageController extends Controller
{
    /**
     * Генерирует и отдает кропленное изображение
     * URL: /image/resize?path=categories/image.jpg&w=300&h=200&mode=crop
     */
    public function actionResize(): Response
    {
        $path = Yii::$app->request->get('path');
        $width = (int)Yii::$app->request->get('w', 300);
        $height = (int)Yii::$app->request->get('h', 200);
        $mode = Yii::$app->request->get('mode', 'crop');

        // Валидация параметров
        if (!$path) {
            throw new NotFoundHttpException('Path parameter is required');
        }

        if ($width <= 0 || $width > 2000) {
            throw new NotFoundHttpException('Invalid width parameter');
        }

        if ($height <= 0 || $height > 2000) {
            throw new NotFoundHttpException('Invalid height parameter');
        }

        if (!in_array($mode, ['crop', 'fit', 'stretch'])) {
            $mode = 'crop';
        }

        // Проверяем безопасность пути
        $cleanPath = $this->sanitizePath($path);
        if (!$cleanPath) {
            throw new NotFoundHttpException('Invalid path');
        }

        /** @var ImageResizeServiceInterface $imageService */
        $imageService = Yii::$container->get(ImageResizeServiceInterface::class);

        // Формируем пути для миниатюр
        $pathInfo = pathinfo($cleanPath);
        $thumbnailName = $pathInfo['filename'] . "_{$width}x{$height}_{$mode}." . $pathInfo['extension'];
        $thumbnailRelativePath = 'thumbnails/' . $pathInfo['dirname'] . '/' . $thumbnailName;

        // Проверяем, существует ли миниатюра
        $fullThumbnailPath = Yii::getAlias('@backend/web/uploads/' . $thumbnailRelativePath);
        
        if (!file_exists($fullThumbnailPath)) {
            // Создаем миниатюру
            $sourceFile = Yii::getAlias('@backend/web/uploads/' . $cleanPath);
            
            Yii::info("Checking source file: {$sourceFile}", __METHOD__);
            
            if (!file_exists($sourceFile)) {
                Yii::error("Source image not found: {$sourceFile}", __METHOD__);
                throw new NotFoundHttpException('Source image not found: ' . $cleanPath);
            }

            // Создаем директорию для миниатюр если не существует
            $thumbnailDir = dirname($fullThumbnailPath);
            if (!is_dir($thumbnailDir)) {
                if (!mkdir($thumbnailDir, 0755, true)) {
                    Yii::error("Failed to create thumbnail directory: {$thumbnailDir}", __METHOD__);
                    throw new NotFoundHttpException('Failed to create thumbnail directory');
                }
            }

            Yii::info("Generating thumbnail: {$sourceFile} -> {$fullThumbnailPath}", __METHOD__);
            
            if (!$imageService->resize($sourceFile, $fullThumbnailPath, $width, $height, $mode)) {
                Yii::error("Failed to generate thumbnail: {$fullThumbnailPath}", __METHOD__);
                throw new NotFoundHttpException('Failed to generate thumbnail');
            }
            
            Yii::info("Thumbnail generated successfully: {$fullThumbnailPath}", __METHOD__);
        }
        
        if (!file_exists($fullThumbnailPath)) {
            throw new NotFoundHttpException('Thumbnail not found');
        }

        // Определяем MIME-тип
        $imageInfo = getimagesize($fullThumbnailPath);
        $mimeType = $imageInfo['mime'] ?? 'image/jpeg';

        // Устанавливаем заголовки для кеширования
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 год
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));

        // Проверяем If-Modified-Since для 304 ответа
        $lastModified = filemtime($fullThumbnailPath);
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $lastModified));

        $ifModifiedSince = Yii::$app->request->headers->get('If-Modified-Since');
        if ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified) {
            $response->statusCode = 304;
            return $response;
        }

        $response->data = file_get_contents($fullThumbnailPath);
        return $response;
    }

    /**
     * Очищает и проверяет путь к файлу
     */
    private function sanitizePath(string $path): ?string
    {
        // Убираем опасные символы
        $path = str_replace(['../', '..\\', '\\'], '', $path);
        
        // Проверяем, что путь начинается с разрешенных директорий
        $allowedDirs = ['categories/', 'products/'];
        $isAllowed = false;
        
        foreach ($allowedDirs as $dir) {
            if (strpos($path, $dir) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return null;
        }

        // Проверяем расширение файла
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return null;
        }

        return $path;
    }
}
