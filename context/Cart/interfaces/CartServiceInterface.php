<?php

namespace context\Cart\interfaces;

use context\Cart\models\Cart;
use repositories\Product\models\Product;

interface CartServiceInterface
{
    public function getCart(): Cart;
    
    public function addProduct(int $productId, int $quantity = 1): bool;
}
