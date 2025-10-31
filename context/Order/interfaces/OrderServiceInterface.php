<?php

namespace context\Order\interfaces;

use context\Cart\models\Cart;
use repositories\Order\models\Order;

interface OrderServiceInterface
{
    public function create(Cart $cart, array $customerData): Order;

    public function findOrderByUuid(string $uuid): ?Order;

    public function prepareViewData(Order $order): array;
}
