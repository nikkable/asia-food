<?php

namespace repositories\Product;

use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Product\models\Product;
use repositories\Category\models\Category;

class ProductRepository implements ProductRepositoryInterface
{
    public function findBySlug(string $slug): ?Product
    {
        return Product::find()->where(['slug' => $slug, 'status' => 1])->one();
    }

    public function findAllByCategory(Category $category): array
    {
        return Product::find()->where(['category_id' => $category->id, 'status' => 1])->all();
    }
}
