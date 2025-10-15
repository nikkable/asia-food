<?php

namespace context\Favorite\interfaces;

use repositories\Favorite\models\FavoriteList;

/**
 * Интерфейс сервиса для работы с избранными товарами
 */
interface FavoriteServiceInterface
{
    public function getFavorites(): FavoriteList;
    
    public function addProduct(int $productId): bool;
    
    public function removeProduct(int $productId): bool;
    
    public function isInFavorites(int $productId): bool;
    
    public function clearFavorites(): bool;
}
