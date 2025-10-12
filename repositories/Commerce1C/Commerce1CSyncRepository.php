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
        return $this->productRepository->findByCondition(['external_id' => $externalId]);
    }

    public function getCategoryByExternalId(string $externalId): ?object
    {
        return $this->categoryRepository->findByCondition(['external_id' => $externalId]);
    }

    private function createCategory(array $categoryData): void
    {
        // TODO: Реализовать создание категории
        // Создать объект Category с данными из CommerceML
        // Сохранить через CategoryRepository
    }

    private function updateCategory(object $category, array $categoryData): void
    {
        // TODO: Реализовать обновление категории
        // Обновить поля категории данными из CommerceML
        // Сохранить через CategoryRepository
    }

    private function createProduct(array $productData): void
    {
        // TODO: Реализовать создание товара
        // Создать объект Product с данными из CommerceML
        // Сохранить через ProductRepository
    }

    private function updateProduct(object $product, array $productData): void
    {
        // TODO: Реализовать обновление товара
        // Обновить поля товара данными из CommerceML
        // Сохранить через ProductRepository
    }

    private function updateProductOffer(object $product, array $offerData): void
    {
        // TODO: Реализовать обновление цен и остатков
        // Обновить price, quantity и другие поля из предложения
        // Сохранить через ProductRepository
    }
}
