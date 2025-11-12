<?php

namespace repositories\Commerce1C;

use repositories\Category\models\Category;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use repositories\Category\interfaces\CategoryRepositoryInterface;
use repositories\Product\interfaces\ProductRepositoryInterface;
use repositories\Product\models\Product;
use yii\db\Exception;

class Commerce1CSyncRepository implements Commerce1CSyncRepositoryInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function syncCategories(array $categoriesData): int
    {
        $syncedCount = 0;
        
        foreach ($categoriesData as $categoryData) {
            try {
                $existingCategory = $this->getCategoryByExternalId($categoryData['external_id']);

                if ($existingCategory) {
                    $this->updateCategory($existingCategory, $categoryData);
                } else {
                    $this->createCategory($categoryData);
                }
                
                $syncedCount++;
            } catch (Exception $e) {
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
                    $this->updateProduct($existingProduct, $productData);
                } else {
                    $this->createProduct($productData);
                }
                
                $syncedCount++;
            } catch (Exception $e) {
                \Yii::error("Failed to sync product {$productData['external_id']}: " . $e->getMessage());
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
                    $this->updateProductOffer($product, $offerData);
                    $syncedCount++;
                } else {
                    \Yii::error("Product not found: name='{$offerData['name']}', external_id='{$offerData['external_id']}'", __METHOD__);
                }
            } catch (Exception $e) {
                \Yii::error("Failed to sync offer {$offerData['external_id']}: " . $e->getMessage());
            }
        }
        
        return $syncedCount;
    }

    public function getProductByExternalId(string $externalId): ?object
    {
        return $this->productRepository->findByExternalId($externalId);
    }

    public function getProductByName(string $name): ?object
    {
        return $this->productRepository->findByName($name);
    }

    public function getCategoryByExternalId(string $externalId): ?object
    {
        return $this->categoryRepository->findByExternalId($externalId);
    }

    public function getCategoryByName(string $name): ?object
    {
        return $this->categoryRepository->findByName($name);
    }

    /**
     * @throws Exception
     */
    private function createCategory(array $categoryData): void
    {
        $category = new Category();
        $category->name = $categoryData['name'];
        $category->description = $categoryData['description'] ?? '';
        $category->external_id = $categoryData['external_id'];
        $category->status = 1;
        
        if (!empty($categoryData['parent_external_id'])) {
            $parentCategory = $this->getCategoryByExternalId($categoryData['parent_external_id']);
            if ($parentCategory) {
                $category->parent_id = $parentCategory->id;
            }
        }
        
        if (!$category->save()) {
            throw new Exception('Failed to save category: ' . json_encode($category->errors));
        }
    }

    /**
     * @throws Exception
     */
    private function updateCategory(object $category, array $categoryData): void
    {
        $category->name = $categoryData['name'];
        $category->description = $categoryData['description'] ?? '';
        $category->external_id = $categoryData['external_id'];
        
        if (!empty($categoryData['parent_external_id'])) {
            $parentCategory = $this->getCategoryByExternalId($categoryData['parent_external_id']);
            $category->parent_id = $parentCategory?->id;
        } else {
            $category->parent_id = null;
        }
        
        if (!$category->save()) {
            throw new Exception('Failed to update category: ' . json_encode($category->errors));
        }
    }

    /**
     * @throws Exception
     */
    private function createProduct(array $productData): void
    {
        $product = new Product();
        $product->name = $productData['name'];
        $product->external_id = $productData['external_id'];
        $product->price = 0;
        $product->quantity = 0;
        $product->status = 1;

        if ($productData['description']) {
            $product->description = $productData['description'];
        }

        if ($productData['article']) {
            $product->article = $productData['article'];
        }
        
        // Если из каталога пришло основное изображение (basename), сохраним его
        if (!empty($productData['image'])) {
            $product->image = $productData['image'];
        }
        
        if (!empty($productData['category_external_id'])) {
            $category = $this->getCategoryByExternalId($productData['category_external_id']);
            if ($category) {
                $product->category_id = $category->id;
            } else {
                $product->category_id = $this->getOrCreateDefaultCategory();
            }
        } else {
            $product->category_id = $this->getOrCreateDefaultCategory();
        }
        
        if (!$product->save()) {
            throw new Exception('Failed to save product: ' . json_encode($product->errors));
        }
    }

    /**
     * @throws Exception
     */
    private function updateProduct(object $product, array $productData): void
    {
        $product->name = $productData['name'];
        $product->external_id = $productData['external_id'];

        if ($productData['description']) {
            $product->description = $productData['description'];
        }

        if ($productData['article']) {
            $product->article = $productData['article'];
        }
        
        // Обновим изображение, если пришло в данных каталога
        if (!empty($productData['image'])) {
            $product->image = $productData['image'];
        }
        
        if (!empty($productData['category_external_id'])) {
            $category = $this->getCategoryByExternalId($productData['category_external_id']);
            if ($category) {
                $product->category_id = $category->id;
            }
        }
        
        if (!$product->save()) {
            throw new Exception('Failed to update product: ' . json_encode($product->errors));
        }
    }

    /**
     * @throws Exception
     */
    private function updateProductOffer(object $product, array $offerData): void
    {
        if (isset($offerData['prices']) && !empty($offerData['prices'])) {
            // Берем первую цену как основную
            $mainPrice = $offerData['prices'][0];
            $product->price = (float)$mainPrice['value'];
        }
        
        if (isset($offerData['quantity'])) {
            $product->quantity = (int)$offerData['quantity'];
        }
        
        if (!$product->save()) {
            throw new Exception('Failed to update product offer: ' . json_encode($product->errors));
        }
    }

    /**
     * @throws Exception
     */
    private function getOrCreateDefaultCategory(): int
    {
        $defaultCategory = Category::find()
            ->where(['name' => 'Импорт из 1С'])
            ->one();
            
        if (!$defaultCategory) {
            $defaultCategory = new Category();
            $defaultCategory->name = 'Импорт из 1С';
            $defaultCategory->description = 'Категория для товаров, импортированных из 1С';
            $defaultCategory->external_id = 'default-1c-import';
            $defaultCategory->status = 1;
            $defaultCategory->save();
        }
        
        return $defaultCategory->id;
    }
}
