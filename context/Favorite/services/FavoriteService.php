<?php

namespace context\Favorite\services;

use context\Favorite\interfaces\FavoriteServiceInterface;
use repositories\Favorite\interfaces\FavoriteRepositoryInterface;
use repositories\Favorite\models\FavoriteList;
use repositories\Product\interfaces\ProductRepositoryInterface;

/**
 * Сервис для работы с избранными товарами
 */
class FavoriteService implements FavoriteServiceInterface
{
    private FavoriteRepositoryInterface $favoriteRepository;
    private ProductRepositoryInterface $productRepository;
    
    /**
     * @param FavoriteRepositoryInterface $favoriteRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        FavoriteRepositoryInterface $favoriteRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->favoriteRepository = $favoriteRepository;
        $this->productRepository = $productRepository;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFavorites(): FavoriteList
    {
        $items = $this->favoriteRepository->getAll();
        return new FavoriteList($items);
    }
    
    /**
     * {@inheritdoc}
     */
    public function addProduct(int $productId): bool
    {
        // Проверяем существование товара
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            return false;
        }
        
        // Проверяем, что товар активен
        if ($product->status !== 1) {
            return false;
        }
        
        return $this->favoriteRepository->add($productId);
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeProduct(int $productId): bool
    {
        return $this->favoriteRepository->remove($productId);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isInFavorites(int $productId): bool
    {
        return $this->favoriteRepository->exists($productId);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearFavorites(): bool
    {
        return $this->favoriteRepository->clear();
    }
}
