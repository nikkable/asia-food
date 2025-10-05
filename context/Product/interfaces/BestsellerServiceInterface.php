<?php

namespace context\Product\interfaces;

use repositories\Product\models\Product;

/**
 * Интерфейс сервиса для работы с хитами продаж
 */
interface BestsellerServiceInterface
{
    /**
     * Получить список хитов продаж
     * 
     * @param int $limit Ограничение количества товаров
     * @return Product[] Массив товаров
     */
    public function getBestsellers(int $limit = 20): array;
}
