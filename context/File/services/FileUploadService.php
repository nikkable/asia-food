<?php

namespace context\File\services;

use context\AbstractService;
use context\File\interfaces\FileUploadServiceInterface;
use context\File\enums\FileTypeEnum;
use yii\web\UploadedFile;

class FileUploadService extends AbstractService implements FileUploadServiceInterface
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    /**
     * @inheritDoc
     */
    public function uploadImage(UploadedFile $file, string $directory, ?string $oldFileName = null): ?string
    {
        try {
            if (!$this->isValidImage($file)) {
                \Yii::error('Invalid image file: ' . $file->name, __METHOD__);
                return null;
            }

            // Создаем директорию если её нет
            $fullPath = \Yii::getAlias('@backend/web/uploads/' . $directory);
            if (!is_dir($fullPath)) {
                if (!mkdir($fullPath, 0755, true)) {
                    \Yii::error('Failed to create directory: ' . $fullPath, __METHOD__);
                    return null;
                }
            }

            // Удаляем старый файл если есть
            if ($oldFileName) {
                $this->deleteFile($oldFileName, $directory);
            }

            // Генерируем новое имя файла
            $fileName = $this->generateFileName($file->name);

            // Сохраняем файл
            $filePath = $fullPath . '/' . $fileName;
            if ($file->saveAs($filePath)) {
                \Yii::info('File uploaded successfully: ' . $filePath, __METHOD__);
                return $fileName;
            } else {
                \Yii::error('Failed to save file: ' . $filePath, __METHOD__);
                return null;
            }
        } catch (\Exception $e) {
            \Yii::error('Exception during file upload: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $fileName, string $directory): bool
    {
        $filePath = \Yii::getAlias('@backend/web/uploads/' . $directory . '/' . $fileName);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isValidImage(UploadedFile $file): bool
    {
        // Проверяем, что файл загружен корректно
        if ($file->error !== UPLOAD_ERR_OK) {
            return false;
        }

        // Проверяем размер файла
        if ($file->size > self::MAX_FILE_SIZE || $file->size <= 0) {
            return false;
        }

        // Проверяем расширение по имени файла
        $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        if (!in_array($extension, FileTypeEnum::getAllowedImageExtensions())) {
            return false;
        }

        // Проверяем MIME-тип более безопасным способом
        if ($file->type && !in_array($file->type, FileTypeEnum::getAllowedImageMimes())) {
            return false;
        }

        // Дополнительная проверка через getimagesize если файл доступен
        if ($file->tempName && file_exists($file->tempName)) {
            $imageInfo = @getimagesize($file->tempName);
            if ($imageInfo === false) {
                return false;
            }
            
            // Проверяем, что это действительно изображение
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
            if (!in_array($imageInfo[2], $allowedTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function generateFileName(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return uniqid() . '_' . time() . '.' . $extension;
    }
}