<?php

namespace context\Cart\services;

use context\AbstractService;
use context\Cart\interfaces\CartServiceInterface;
use context\Cart\models\Cart;
use repositories\Product\models\Product;

class CartService extends AbstractService implements CartServiceInterface
{
    public function __construct(
        private readonly Cart $cart
    ) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }
    
    public function addProduct(int $productId, int $quantity = 1): bool
    {
        $product = Product::findOne($productId);
        
        if (!$product || $product->status !== 1 || $product->quantity < $quantity) {
            return false;
        }
        
        $this->cart->add($product, $quantity);
        return true;
    }
}
