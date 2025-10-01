<?php

namespace repositories\Order;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        if (!$order->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }
}
