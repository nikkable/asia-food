<?php

namespace context\Cart\services;

use context\AbstractService;
use context\Cart\interfaces\CartServiceInterface;
use context\Cart\models\Cart;

class CartService extends AbstractService implements CartServiceInterface
{
    public function __construct(
        private readonly Cart $cart
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
