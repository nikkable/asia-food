<?php

namespace common\helpers;

/**
 * Хелпер для форматирования цен
 */
class PriceHelper
{
    /**
     * Форматирует цену в рублях
     */
    public static function format(float $price, bool $showCurrency = true) :string
    {
        if ($showCurrency) {
            return self::formatRub($price);
        } else {
            return number_format($price, 0, ',', ' ');
        }
    }
    
    /**
     * Форматирует цену в рублях с символом ₽
     */
    public static function formatRub(float $price) :string
    {
        return number_format($price, 0, ',', ' ') . ' ₽';
    }
    
    /**
     * Форматирует цену в рублях для отображения в GridView
     */
    public static function formatForGrid(float $price) :string
    {
        return self::formatRub($price);
    }
}
