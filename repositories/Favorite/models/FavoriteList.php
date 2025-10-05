<?php

namespace repositories\Favorite\models;

/**
 * Модель списка избранных товаров
 */
class FavoriteList
{
    /**
     * @var Favorite[] Список избранных товаров
     */
    private array $items = [];
    
    /**
     * @param Favorite[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    /**
     * Получить все избранные товары
     * 
     * @return Favorite[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * Добавить товар в избранное
     * 
     * @param Favorite $favorite
     */
    public function addItem(Favorite $favorite): void
    {
        $this->items[$favorite->getProductId()] = $favorite;
    }
    
    /**
     * Удалить товар из избранного
     * 
     * @param int $productId
     */
    public function removeItem(int $productId): void
    {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
        }
    }
    
    /**
     * Проверить, есть ли товар в избранном
     * 
     * @param int $productId
     * @return bool
     */
    public function hasItem(int $productId): bool
    {
        return isset($this->items[$productId]);
    }
    
    /**
     * Очистить избранное
     */
    public function clear(): void
    {
        $this->items = [];
    }
    
    /**
     * Получить количество товаров в избранном
     * 
     * @return int
     */
    public function getCount(): int
    {
        return count($this->items);
    }
}
