<?php

namespace repositories\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductRepositoryInterface
{
    /**
     * Найти товар по ID
     */
    public function findById(int $id): ?Product;

    /**
     * Найти товар по slug
     */
    public function findBySlug(string $slug): ?Product;
    
    /**
     * Найти все товары с пагинацией
     */
    public function findAll(int $limit = null, int $offset = null): array;

    /**
     * Найти все товары в категории с пагинацией
     */
    public function findAllByCategory(Category $category, int $limit = null, int $offset = null): array;
    
    /**
     * Получить общее количество товаров
     */
    public function countAll(): int;
    
    /**
     * Получить количество товаров в категории
     */
    public function countByCategory(Category $category): int;

    /**
     * Поиск товаров по названию
     */
    public function searchByName(string $query, int $limit = 10): array;
}
