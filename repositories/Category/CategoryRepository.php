<?php

namespace repositories\Category;

use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Category\models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function findBySlug(string $slug): ?Category
    {
        return Category::find()->where(['slug' => $slug, 'status' => 1])->one();
    }

    public function getRoot(): array
    {
        return Category::find()->where(['parent_id' => null, 'status' => 1])->all();
    }
    
    public function findByExternalId(string $externalId): ?Category
    {
        return Category::find()->where(['external_id' => $externalId])->one();
    }
    
    public function findByName(string $name): ?Category
    {
        return Category::find()->where(['name' => $name])->one();
    }
}
