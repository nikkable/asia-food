<?php

namespace repositories\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;
    
    /**
     * Найти все товары с пагинацией
     * 
     * @param int $limit Количество товаров на странице
     * @param int $offset Смещение
     * @return array Массив товаров
     */
    public function findAll(int $limit = null, int $offset = null): array;

    /**
     * Найти все товары в категории с пагинацией
     * 
     * @param Category $category Категория
     * @param int $limit Количество товаров на странице
     * @param int $offset Смещение
     * @return array Массив товаров
     */
    public function findAllByCategory(Category $category, int $limit = null, int $offset = null): array;
    
    /**
     * Получить общее количество товаров
     * 
     * @return int Количество товаров
     */
    public function countAll(): int;
    
    /**
     * Получить количество товаров в категории
     * 
     * @param Category $category Категория
     * @return int Количество товаров
     */
    public function countByCategory(Category $category): int;
}
