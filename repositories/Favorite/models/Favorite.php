<?php

namespace repositories\Favorite\models;

use repositories\Product\models\Product;

/**
 * Модель избранного товара
 */
class Favorite
{
    private int $productId;
    private ?Product $product = null;
    
    /**
     * @param int $productId ID товара
     * @param Product|null $product Модель товара
     */
    public function __construct(int $productId, ?Product $product = null)
    {
        $this->productId = $productId;
        $this->product = $product;
    }
    
    /**
     * Получить ID товара
     * 
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
    
    /**
     * Получить модель товара
     * 
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    
    /**
     * Установить модель товара
     * 
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
