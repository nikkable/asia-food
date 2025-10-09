<?php

namespace context\File\services;

use context\AbstractService;
use context\File\interfaces\FileUploadServiceInterface;
use context\File\enums\FileTypeEnum;
use context\File\interfaces\FileInterface;

class FileUploadService extends AbstractService implements FileUploadServiceInterface
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    private ?string $lastError = null;

    /**
     * @inheritDoc
     */
    public function uploadImage(FileInterface $file, string $directory, ?string $oldFileName = null): ?string
    {
        $this->lastError = null; // Сбрасываем предыдущую ошибку
        try {
            if (!$this->isValidImage($file)) {
                // Ошибка уже установлена в isValidImage
                \Yii::error('Invalid image file: ' . $file->getName() . '. Reason: ' . $this->lastError, __METHOD__);
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
            $fileName = $this->generateFileName($file->getName());

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
    public function isValidImage(FileInterface $file): bool
    {
        // Проверяем, что файл загружен корректно
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $this->lastError = 'Ошибка загрузки файла (код: ' . $file->getError() . ').';
            return false;
        }

        // Проверяем размер файла
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $this->lastError = 'Файл слишком большой. Максимальный размер: ' . self::MAX_FILE_SIZE . ' байт.';
            return false;
        }
        if ($file->getSize() <= 0) {
            $this->lastError = 'Файл пустой.';
            return false;
        }

        // Проверяем расширение по имени файла
        $extension = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION));
        if (!in_array($extension, FileTypeEnum::getAllowedImageExtensions())) {
            $this->lastError = 'Недопустимое расширение файла: ' . $extension;
            return false;
        }

        // Проверяем MIME-тип, если он доступен
        if ($file->getType() && !in_array($file->getType(), FileTypeEnum::getAllowedImageMimes())) {
            // Это может быть ложным срабатыванием, поэтому просто логируем, но не блокируем
            \Yii::warning('MIME-тип файла (' . $file->getType() . ') не соответствует ожидаемому, но проверка продолжается.', __METHOD__);
        }

        // Самая надежная проверка - через getimagesize
        $tempName = $file->getTempName();
        if ($tempName && file_exists($tempName)) {
            $imageInfo = @getimagesize($tempName);
            if ($imageInfo === false) {
                $this->lastError = 'Не удалось прочитать информацию об изображении. Возможно, файл поврежден или не является изображением.';
                return false;
            }
            
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
            if (!in_array($imageInfo[2], $allowedTypes)) {
                $this->lastError = 'Тип изображения не поддерживается (определен как ' . $imageInfo[2] . ').';
                return false;
            }
        } else {
            $this->lastError = 'Временный файл не найден для проверки.';
            return false;
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

    /**
     * @inheritDoc
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}