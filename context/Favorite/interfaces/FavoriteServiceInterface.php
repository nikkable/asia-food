<?php

namespace context\Favorite\interfaces;

use repositories\Favorite\models\FavoriteList;

/**
 * Интерфейс сервиса для работы с избранными товарами
 */
interface FavoriteServiceInterface
{
    /**
     * Получить список избранных товаров
     * 
     * @return FavoriteList
     */
    public function getFavorites(): FavoriteList;
    
    /**
     * Добавить товар в избранное
     * 
     * @param int $productId
     * @return bool
     */
    public function addProduct(int $productId): bool;
    
    /**
     * Удалить товар из избранного
     * 
     * @param int $productId
     * @return bool
     */
    public function removeProduct(int $productId): bool;
    
    /**
     * Проверить, есть ли товар в избранном
     * 
     * @param int $productId
     * @return bool
     */
    public function isInFavorites(int $productId): bool;
    
    /**
     * Очистить список избранных товаров
     * 
     * @return bool
     */
    public function clearFavorites(): bool;
}
