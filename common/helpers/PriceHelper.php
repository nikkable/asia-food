<?php

namespace common\helpers;

use Yii;

/**
 * Хелпер для форматирования цен
 */
class PriceHelper
{
    /**
     * Форматирует цену в рублях
     * 
     * @param float $price Цена
     * @param bool $showCurrency Показывать символ валюты
     * @return string Отформатированная цена
     */
    public static function format($price, $showCurrency = true)
    {
        if ($showCurrency) {
            return self::formatRub($price);
        } else {
            return number_format($price, 0, ',', ' ');
        }
    }
    
    /**
     * Форматирует цену в рублях с символом ₽
     * 
     * @param float $price Цена
     * @return string Отформатированная цена
     */
    public static function formatRub($price)
    {
        return number_format($price, 0, ',', ' ') . ' ₽';
    }
    
    /**
     * Форматирует цену в рублях для отображения в GridView
     * 
     * @param float $price Цена
     * @return string Отформатированная цена
     */
    public static function formatForGrid($price)
    {
        return self::formatRub($price);
    }
}
