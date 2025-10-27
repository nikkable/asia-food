<?php

namespace repositories\Order;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;
use yii\db\Exception;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function save(Order $order): void
    {
        if (!$order->save()) {
            $errors = json_encode($order->getErrors());
            throw new \RuntimeException("Saving error. Details: {$errors}");
        }
    }
    
    public function findById(int $id): ?Order
    {
        return Order::find()
            ->where(['id' => $id])
            ->with('orderItems')
            ->one();
    }
}
