<?php

namespace context\Delivery\interfaces;

use context\Cart\models\Cart;

interface DeliveryServiceInterface
{
    public function calculate(Cart $cart, string $method): array;
}
