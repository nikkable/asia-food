<?php

namespace repositories\Commerce1C;

use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;

class Commerce1CSyncRepository implements Commerce1CSyncRepositoryInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function syncCategories(array $categoriesData): int
    {
        $syncedCount = 0;
        
        foreach ($categoriesData as $categoryData) {
            try {
                $existingCategory = $this->getCategoryByExternalId($categoryData['external_id']);
                
                if ($existingCategory) {
                    // Обновляем существующую категорию
                    $this->updateCategory($existingCategory, $categoryData);
                } else {
                    // Создаем новую категорию
                    $this->createCategory($categoryData);
                }
                
                $syncedCount++;
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем синхронизацию
                error_log("Failed to sync category {$categoryData['external_id']}: " . $e->getMessage());
            }
        }
        
        return $syncedCount;
    }

    public function syncProducts(array $productsData): int
    {
        $syncedCount = 0;
        
        foreach ($productsData as $productData) {
            try {
                $existingProduct = $this->getProductByExternalId($productData['external_id']);
                
                if ($existingProduct) {
                    // Обновляем существующий товар
                    $this->updateProduct($existingProduct, $productData);
                } else {
                    // Создаем новый товар
                    $this->createProduct($productData);
                }
                
                $syncedCount++;
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем синхронизацию
                error_log("Failed to sync product {$productData['external_id']}: " . $e->getMessage());
            }
        }
        
        return $syncedCount;
    }

    public function syncOffers(array $offersData): int
    {
        $syncedCount = 0;
        
        foreach ($offersData as $offerData) {
            try {
                $product = $this->getProductByExternalId($offerData['external_id']);
                
                if ($product) {
                    // Обновляем цену и остатки
                    $this->updateProductOffer($product, $offerData);
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем синхронизацию
                error_log("Failed to sync offer {$offerData['external_id']}: " . $e->getMessage());
            }
        }
        
        return $syncedCount;
    }

    public function getProductByExternalId(string $externalId): ?object
    {
        return $this->productRepository->findByExternalId($externalId);
    }

    public function getCategoryByExternalId(string $externalId): ?object
    {
        return $this->categoryRepository->findByExternalId($externalId);
    }

    private function createCategory(array $categoryData): void
    {
        $category = new \repositories\Category\models\Category();
        $category->name = $categoryData['name'];
        $category->description = $categoryData['description'] ?? '';
        $category->external_id = $categoryData['external_id'];
        $category->status = 1; // Активная
        
        // Поиск родительской категории
        if (!empty($categoryData['parent_external_id'])) {
            $parentCategory = $this->getCategoryByExternalId($categoryData['parent_external_id']);
            if ($parentCategory) {
                $category->parent_id = $parentCategory->id;
            }
        }
        
        if (!$category->save()) {
            throw new \Exception('Failed to save category: ' . json_encode($category->errors));
        }
        
        \Yii::error("Created category: {$category->name} (ID: {$category->id})", __METHOD__);
    }

    private function updateCategory(object $category, array $categoryData): void
    {
        $category->name = $categoryData['name'];
        $category->description = $categoryData['description'] ?? '';
        
        // Обновляем родительскую категорию
        if (!empty($categoryData['parent_external_id'])) {
            $parentCategory = $this->getCategoryByExternalId($categoryData['parent_external_id']);
            $category->parent_id = $parentCategory ? $parentCategory->id : null;
        } else {
            $category->parent_id = null;
        }
        
        if (!$category->save()) {
            throw new \Exception('Failed to update category: ' . json_encode($category->errors));
        }
        
        \Yii::error("Updated category: {$category->name} (ID: {$category->id})", __METHOD__);
    }

    private function createProduct(array $productData): void
    {
        $product = new \repositories\Product\models\Product();
        $product->name = $productData['name'];
        $product->description = $productData['description'] ?? '';
        $product->article = $productData['article'] ?? '';
        $product->external_id = $productData['external_id'];
        $product->price = 0; // Цена будет обновлена в offers
        $product->quantity = 0; // Остаток будет обновлен в offers
        $product->status = 1; // Активный
        
        // Поиск категории
        if (!empty($productData['category_external_id'])) {
            $category = $this->getCategoryByExternalId($productData['category_external_id']);
            if ($category) {
                $product->category_id = $category->id;
            } else {
                // Если категория не найдена, создаем дефолтную
                $product->category_id = $this->getOrCreateDefaultCategory();
            }
        } else {
            $product->category_id = $this->getOrCreateDefaultCategory();
        }
        
        if (!$product->save()) {
            throw new \Exception('Failed to save product: ' . json_encode($product->errors));
        }
        
        \Yii::error("Created product: {$product->name} (ID: {$product->id})", __METHOD__);
    }

    private function updateProduct(object $product, array $productData): void
    {
        $product->name = $productData['name'];
        $product->description = $productData['description'] ?? '';
        $product->article = $productData['article'] ?? '';
        
        // Обновляем категорию
        if (!empty($productData['category_external_id'])) {
            $category = $this->getCategoryByExternalId($productData['category_external_id']);
            if ($category) {
                $product->category_id = $category->id;
            }
        }
        
        if (!$product->save()) {
            throw new \Exception('Failed to update product: ' . json_encode($product->errors));
        }
        
        \Yii::error("Updated product: {$product->name} (ID: {$product->id})", __METHOD__);
    }

    private function updateProductOffer(object $product, array $offerData): void
    {
        // Обновляем цену
        if (isset($offerData['price'])) {
            $product->price = (float)$offerData['price'];
        }
        
        // Обновляем остаток
        if (isset($offerData['quantity'])) {
            $product->quantity = (int)$offerData['quantity'];
        }
        
        if (!$product->save()) {
            throw new \Exception('Failed to update product offer: ' . json_encode($product->errors));
        }
        
        \Yii::error("Updated offer for product: {$product->name} (Price: {$product->price}, Qty: {$product->quantity})", __METHOD__);
    }
    
    private function getOrCreateDefaultCategory(): int
    {
        // Поиск дефолтной категории
        $defaultCategory = \repositories\Category\models\Category::find()
            ->where(['name' => 'Импорт из 1С'])
            ->one();
            
        if (!$defaultCategory) {
            // Создаем дефолтную категорию
            $defaultCategory = new \repositories\Category\models\Category();
            $defaultCategory->name = 'Импорт из 1С';
            $defaultCategory->description = 'Категория для товаров, импортированных из 1С';
            $defaultCategory->external_id = 'default-1c-import';
            $defaultCategory->status = 1;
            $defaultCategory->save();
        }
        
        return $defaultCategory->id;
    }
}
