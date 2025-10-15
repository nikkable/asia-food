<?php

namespace repositories\Favorite\interfaces;

/**
 * Интерфейс репозитория для работы с избранными товарами
 */
interface FavoriteRepositoryInterface
{
    /**
     * Получить все избранные товары
     */
    public function getAll(): array;
    
    /**
     * Добавить товар в избранное
     */
    public function add(int $productId): bool;
    
    /**
     * Удалить товар из избранного
     */
    public function remove(int $productId): bool;
    
    /**
     * Проверить, есть ли товар в избранном
     */
    public function exists(int $productId): bool;
    
    /**
     * Очистить избранное
     */
    public function clear(): bool;
    
    /**
     * Получить количество товаров в избранном
     */
    public function getCount(): int;
}
