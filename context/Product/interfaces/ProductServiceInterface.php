<?php

namespace context\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductServiceInterface
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;
    
    public function findAll(int $limit = null, int $offset = null): array;

    public function findAllByCategory(Category $category, int $limit = null, int $offset = null): array;
    
    public function countAll(): int;
    
    public function countByCategory(Category $category): int;

    public function searchByName(string $query, int $limit = 10): array;
}
