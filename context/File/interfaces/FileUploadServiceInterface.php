<?php

namespace context\File\interfaces;

use yii\web\UploadedFile;

interface FileUploadServiceInterface
{
    /**
     * Загружает изображение в указанную папку
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldFileName
     * @return string|null Имя загруженного файла или null при ошибке
     */
    public function uploadImage(UploadedFile $file, string $directory, ?string $oldFileName = null): ?string;

    /**
     * Удаляет файл
     *
     * @param string $fileName
     * @param string $directory
     * @return bool
     */
    public function deleteFile(string $fileName, string $directory): bool;

    /**
     * Проверяет, является ли файл изображением
     *
     * @param UploadedFile $file
     * @return bool
     */
    public function isValidImage(UploadedFile $file): bool;

    /**
     * Генерирует уникальное имя файла
     *
     * @param string $originalName
     * @return string
     */
    public function generateFileName(string $originalName): string;
}