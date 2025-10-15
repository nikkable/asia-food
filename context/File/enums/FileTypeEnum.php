<?php

namespace context\File\enums;

enum FileTypeEnum: string
{
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_PNG = 'image/png';
    case IMAGE_GIF = 'image/gif';
    case IMAGE_WEBP = 'image/webp';

    /**
     * Получить все допустимые MIME-типы изображений
     */
    public static function getAllowedImageMimes(): array
    {
        return [
            self::IMAGE_JPEG->value,
            self::IMAGE_PNG->value,
            self::IMAGE_GIF->value,
            self::IMAGE_WEBP->value,
        ];
    }

    /**
     * Получить все допустимые расширения изображений
     */
    public static function getAllowedImageExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }
}