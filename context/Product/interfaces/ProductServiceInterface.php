<?php

namespace context\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductServiceInterface
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function findAllByCategory(Category $category): array;
}
