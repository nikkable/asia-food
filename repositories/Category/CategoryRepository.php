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
        $categories = Category::find()->where(['parent_id' => null, 'status' => 1])->all();

        // Фильтруем категории, оставляя только те, у которых есть товары в наличии
        return array_filter($categories, function($category) {
            $count = \repositories\Product\models\Product::find()
                ->where(['category_id' => $category->id, 'status' => 1])
                ->andWhere(['>', 'quantity', 0])
                ->count();
            return $count > 0;
        });
    }
    
    public function findByExternalId(string $externalId): ?Category
    {
        return Category::find()->where(['external_id' => $externalId])->one();
    }
    
    public function findByName(string $name): ?Category
    {
        return Category::find()->where(['name' => $name])->one();
    }
    
    public function getRootWithChildren(): array
    {
        // Получаем корневые категории с активными товарами
        $rootCategories = Category::find()
            ->where(['parent_id' => null, 'status' => 1])
            ->with(['children' => function($query) {
                $query->where(['status' => 1]);
            }])
            ->all();

        // Фильтруем корневые категории, оставляя только те, у которых есть товары в наличии
        $filteredRootCategories = array_filter($rootCategories, function($category) {
            $count = \repositories\Product\models\Product::find()
                ->where(['category_id' => $category->id, 'status' => 1])
                ->andWhere(['>', 'quantity', 0])
                ->count();
            return $count > 0;
        });

        // Собираем плоский массив: корневые категории + их дочерние категории
        $result = [];
        foreach ($filteredRootCategories as $rootCategory) {
            // Добавляем корневую категорию
            $result[] = $rootCategory;
            
            // Фильтруем и добавляем дочерние категории
            $filteredChildren = array_filter($rootCategory->children, function($child) {
                $count = \repositories\Product\models\Product::find()
                    ->where(['category_id' => $child->id, 'status' => 1])
                    ->andWhere(['>', 'quantity', 0])
                    ->count();
                return $count > 0;
            });
            
            // Добавляем дочерние категории в общий массив
            foreach ($filteredChildren as $child) {
                $result[] = $child;
            }
        }

        return $result;
    }
}
