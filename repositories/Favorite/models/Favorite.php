<?php

namespace repositories\Favorite\models;

use repositories\Product\models\Product;

/**
 * Модель избранного товара
 */
class Favorite
{
    private int $productId;
    private ?Product $product;
    
    public function __construct(int $productId, ?Product $product = null)
    {
        $this->productId = $productId;
        $this->product = $product;
    }
    
    /**
     * Получить ID товара
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
    
    /**
     * Получить модель товара
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    
    /**
     * Установить модель товара
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
