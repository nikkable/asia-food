<?php

namespace context\Product\interfaces;

use repositories\Product\models\Product;
use repositories\Category\models\Category;

interface ProductServiceInterface
{
    public function findBySlug(string $slug): ?Product;

    public function findAllByCategory(Category $category): array;
}
