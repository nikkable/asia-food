<?php

namespace repositories\Order;

use repositories\Order\interfaces\OrderRepositoryInterface;
use repositories\Order\models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        if (!$order->save()) {
            $errors = json_encode($order->getErrors());
            \Yii::error("\u041eшибка сохранения заказа: {$errors}", 'order');
            throw new \RuntimeException("Saving error. Details: {$errors}");
        }
    }
}
