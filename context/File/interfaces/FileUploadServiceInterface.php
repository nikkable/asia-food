<?php

namespace context\File\interfaces;

interface FileUploadServiceInterface
{
    /**
     * Загружает изображение в указанную папку
     */
    public function uploadImage(FileInterface $file, string $directory, ?string $oldFileName = null): ?string;

    /**
     * Удаляет файл
     */
    public function deleteFile(string $fileName, string $directory): bool;

    /**
     * Проверяет, является ли файл изображением
     */
    public function isValidImage(FileInterface $file): bool;

    /**
     * Генерирует уникальное имя файла
     */
    public function generateFileName(string $originalName): string;

    /**
     * Возвращает сообщение о последней ошибке валидации или загрузки.
     */
    public function getLastError(): ?string;
}