<?php

namespace repositories\Commerce1C\interfaces;

interface Commerce1CSyncRepositoryInterface
{
    /**
     * Синхронизирует категории из CommerceML
     */
    public function syncCategories(array $categoriesData): int;
    
    /**
     * Синхронизирует товары из CommerceML
     */
    public function syncProducts(array $productsData): int;
    
    /**
     * Обновляет остатки и цены товаров
     */
    public function syncOffers(array $offersData): int;
    
    /**
     * Получает товар по внешнему ID (1C)
     */
    public function getProductByExternalId(string $externalId): ?object;
    
    /**
     * Получает категорию по внешнему ID (1C)
     */
    public function getCategoryByExternalId(string $externalId): ?object;
}
