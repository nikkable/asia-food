<?php

namespace repositories\Product;

use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Product\models\Product;
use repositories\Category\models\Category;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::find()->where(['id' => $id, 'status' => 1])->one();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::find()->where(['slug' => $slug, 'status' => 1])->one();
    }

    public function findAll(int $limit = null, int $offset = null): array
    {
        $query = Product::find()->where(['status' => 1])->orderBy(['id' => SORT_DESC]);
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        if ($offset !== null) {
            $query->offset($offset);
        }
        
        return $query->all();
    }
    
    public function findAllByCategory(Category $category, int $limit = null, int $offset = null): array
    {
        $query = Product::find()->where(['category_id' => $category->id, 'status' => 1])->orderBy(['id' => SORT_DESC]);
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        if ($offset !== null) {
            $query->offset($offset);
        }
        
        return $query->all();
    }
    
    public function countAll(): int
    {
        return Product::find()->where(['status' => 1])->count();
    }
    
    public function countByCategory(Category $category): int
    {
        return Product::find()->where(['category_id' => $category->id, 'status' => 1])->count();
    }
}
