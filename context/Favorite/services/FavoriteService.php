<?php

namespace context\Favorite\services;

use context\Favorite\interfaces\FavoriteServiceInterface;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Favorite\models\FavoriteList;
use repositories\Product\interfaces\ProductRepositoryInterface;

/**
 * Сервис для работы с избранными товарами
 */
readonly class FavoriteService implements FavoriteServiceInterface
{
    public function __construct(
        private FavoriteRepositoryInterface $favoriteRepository,
        private ProductRepositoryInterface  $productRepository
    ) {
    }
    
    public function getFavorites(): FavoriteList
    {
        $items = $this->favoriteRepository->getAll();
        return new FavoriteList($items);
    }
    
    public function addProduct(int $productId): bool
    {
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return false;
        }
        
        if ($product->status !== 1) {
            return false;
        }
        
        return $this->favoriteRepository->add($productId);
    }
    
    public function removeProduct(int $productId): bool
    {
        return $this->favoriteRepository->remove($productId);
    }
    
    public function isInFavorites(int $productId): bool
    {
        return $this->favoriteRepository->exists($productId);
    }
    
    public function clearFavorites(): bool
    {
        return $this->favoriteRepository->clear();
    }
}
