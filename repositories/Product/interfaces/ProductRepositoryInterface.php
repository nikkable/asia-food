<?php

namespace repositories\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function findAllByCategory(Category $category): array;
}
