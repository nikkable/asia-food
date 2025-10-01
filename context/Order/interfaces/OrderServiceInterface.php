<?php

namespace context\Order\interfaces;

use context\Cart\models\Cart;
use repositories\Order\models\Order;

interface OrderServiceInterface
{
    public function create(Cart $cart, array $customerData): Order;
}
