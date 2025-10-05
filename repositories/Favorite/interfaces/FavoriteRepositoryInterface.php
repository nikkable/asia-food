<?php

namespace repositories\Favorite\interfaces;

use repositories\Favorite\models\Favorite;

/**
 * Интерфейс репозитория для работы с избранными товарами
 */
interface FavoriteRepositoryInterface
{
    /**
     * Получить все избранные товары
     * 
     * @return array
     */
    public function getAll(): array;
    
    /**
     * Добавить товар в избранное
     * 
     * @param int $productId ID товара
     * @return bool
     */
    public function add(int $productId): bool;
    
    /**
     * Удалить товар из избранного
     * 
     * @param int $productId ID товара
     * @return bool
     */
    public function remove(int $productId): bool;
    
    /**
     * Проверить, есть ли товар в избранном
     * 
     * @param int $productId ID товара
     * @return bool
     */
    public function exists(int $productId): bool;
    
    /**
     * Очистить избранное
     * 
     * @return bool
     */
    public function clear(): bool;
    
    /**
     * Получить количество товаров в избранном
     * 
     * @return int
     */
    public function getCount(): int;
}
