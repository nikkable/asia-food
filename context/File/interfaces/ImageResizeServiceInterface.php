<?php

namespace context\File\interfaces;

interface ImageResizeServiceInterface
{
    /**
     * Создает миниатюру изображения с заданными размерами
     * @param string $sourceFile Путь к исходному файлу
     * @param string $targetFile Путь к целевому файлу
     * @param int $width Ширина
     * @param int $height Высота
     * @param string $mode Режим ресайза: 'crop', 'fit', 'stretch'
     * @return bool
     */
    public function resize(string $sourceFile, string $targetFile, int $width, int $height, string $mode = 'crop'): bool;

    /**
     * Генерирует URL для изображения с заданными параметрами
     * @param string $originalPath Путь к оригинальному изображению
     * @param int $width Ширина
     * @param int $height Высота
     * @param string $mode Режим ресайза
     * @return string
     */
    public function getResizedImageUrl(string $originalPath, int $width, int $height, string $mode = 'crop'): string;

    /**
     * Получает размеры изображения
     * @param string $filePath Путь к файлу
     * @return array|false [width, height] или false при ошибке
     */
    public function getImageSize(string $filePath): array|false;

    /**
     * Проверяет, существует ли миниатюра
     * @param string $thumbnailPath Путь к миниатюре
     * @return bool
     */
    public function thumbnailExists(string $thumbnailPath): bool;

    /**
     * Удаляет все миниатюры для изображения
     * @param string $originalPath Путь к оригинальному изображению
     * @return bool
     */
    public function deleteThumbnails(string $originalPath): bool;
}
