<?php

namespace repositories\Product\interfaces;

use repositories\Product\models\Product;

/**
 * Интерфейс репозитория для работы с хитами продаж
 */
interface BestsellerRepositoryInterface
{
    /**
     * Получить список хитов продаж
     * @return Product[] Массив товаров
     */
    public function getBestsellers(int $limit = 20): array;
}
