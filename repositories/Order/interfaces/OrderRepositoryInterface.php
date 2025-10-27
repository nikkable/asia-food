<?php

namespace repositories\Order\interfaces;

use repositories\Order\models\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    
    public function findById(int $id): ?Order;
}
